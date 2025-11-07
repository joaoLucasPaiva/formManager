<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\FormAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormSubmissionController extends Controller
{
    /**
     * Exibe o formulário público
     */
    public function show(string $slug)
    {
        // Busca o formulário pelo slug
        $form = Form::where('slug', $slug)
            ->where('is_published', true)
            ->with(['sections' => function ($query) {
                $query->orderBy('position');
            }, 'fields' => function ($query) {
                $query->where('active', true)
                    ->orderBy('position')
                    ->with('options');
            }])
            ->firstOrFail();

        // Organiza os campos por seção
        $fieldsBySection = $form->fields->groupBy('form_section_id');

        return view('forms.show', [
            'form' => $form,
            'sections' => $form->sections,
            'fieldsBySection' => $fieldsBySection,
        ]);
    }

    /**
     * Processa a submissão do formulário
     */
    public function submit(Request $request, string $slug)
    {
        // Busca o formulário
        $form = Form::where('slug', $slug)
            ->where('is_published', true)
            ->with(['fields' => function ($query) {
                $query->where('active', true);
            }])
            ->firstOrFail();

        // Monta as regras de validação dinamicamente
        $rules = [];
        $messages = [];
        $customAttributes = [];

        foreach ($form->fields as $field) {
            $fieldRules = [];

            if ($field->required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Adiciona regras específicas por tipo de campo
            switch ($field->type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'datetime':
                    $fieldRules[] = 'date';
                    break;
                case 'file':
                    $fieldRules[] = 'file';
                    // Valida tipo de arquivo e tamanho se configurado no campo

                    // Suporta tanto 'mimes' quanto 'extensions' (dos templates)
                    if (isset($field->validation['mimes'])) {
                        $fieldRules[] = 'mimes:' . $field->validation['mimes'];
                    } elseif (isset($field->validation['extensions'])) {
                        $extensions = is_array($field->validation['extensions'])
                            ? implode(',', $field->validation['extensions'])
                            : $field->validation['extensions'];
                        $fieldRules[] = 'mimes:' . $extensions;
                    }

                    // Suporta tanto 'max' quanto 'max_size' (dos templates)
                    if (isset($field->validation['max'])) {
                        $fieldRules[] = 'max:' . $field->validation['max'];
                    } elseif (isset($field->validation['max_size'])) {
                        $fieldRules[] = 'max:' . $field->validation['max_size'];
                    }
                    break;
                case 'checkbox':
                    $fieldRules[] = 'array';
                    break;
            }

            // Adiciona validações customizadas do campo
            if (isset($field->validation) && is_array($field->validation)) {
                // Atributos HTML que não são validações do Laravel
                $htmlAttributes = ['step', 'placeholder', 'pattern', 'autocomplete'];

                foreach ($field->validation as $rule => $value) {
                    // Para arquivos, ignora regras já tratadas no switch case
                    if ($field->type === 'file' && in_array($rule, ['mimes', 'max', 'extensions', 'max_size'])) {
                        continue;
                    }

                    // Ignora atributos HTML puros
                    if (in_array($rule, $htmlAttributes)) {
                        continue;
                    }

                    // Se o valor for array, converte para string separada por vírgula
                    $valueStr = is_array($value) ? implode(',', $value) : $value;
                    $fieldRules[] = "$rule:$valueStr";
                }
            }

            $rules['fields.' . $field->id] = $fieldRules;
            $customAttributes['fields.' . $field->id] = $field->label;

            // Mensagens customizadas em português
            $fieldKey = 'fields.' . $field->id;
            $messages[$fieldKey . '.required'] = "O campo '{$field->label}' é obrigatório.";
            $messages[$fieldKey . '.email'] = "O campo '{$field->label}' deve ser um endereço de e-mail válido.";
            $messages[$fieldKey . '.numeric'] = "O campo '{$field->label}' deve ser um número.";
            $messages[$fieldKey . '.date'] = "O campo '{$field->label}' deve ser uma data válida.";
            $messages[$fieldKey . '.file'] = "O campo '{$field->label}' deve ser um arquivo.";
            $messages[$fieldKey . '.mimes'] = "O campo '{$field->label}' deve ser um arquivo do tipo: :values.";
            $messages[$fieldKey . '.max'] = "O campo '{$field->label}' não pode ser maior que :max kilobytes.";
            $messages[$fieldKey . '.array'] = "O campo '{$field->label}' deve ser uma seleção múltipla.";
            $messages[$fieldKey . '.min'] = "O campo '{$field->label}' deve ter no mínimo :min caracteres.";
            $messages[$fieldKey . '.regex'] = "O formato do campo '{$field->label}' é inválido.";
        }

        // Valida os dados
        $validator = Validator::make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Inicia transação para garantir consistência
        DB::beginTransaction();
        try {
            // Busca o campo identificador para capturar seu valor
            $identifierField = $form->fields->firstWhere('is_identifier', true);
            $identifierValue = null;

            if ($identifierField) {
                $identifierValue = $request->input('fields.' . $identifierField->id);
            }

            // Cria a submissão
            $submission = FormSubmission::create([
                'form_id' => $form->id,
                'identifier_value' => $identifierValue,
                'submitter_type' => null,
                'submitter_id' => null,
                'external_ref' => null,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Salva as respostas de cada campo
            foreach ($form->fields as $field) {
                $value = $request->input('fields.' . $field->id);

                // Pula campos não preenchidos (e não obrigatórios)
                if ($value === null) {
                    continue;
                }

                $answerData = [
                    'form_submission_id' => $submission->id,
                    'form_field_id' => $field->id,
                ];

                // Processa o valor baseado no tipo de campo
                switch ($field->type) {
                    case 'file':
                        // Faz upload do arquivo
                        if ($request->hasFile('fields.' . $field->id)) {
                            $file = $request->file('fields.' . $field->id);
                            $path = $file->store('form-submissions/' . $form->id, 'public');
                            $answerData['value'] = $path;
                            $answerData['json_value'] = [
                                'original_name' => $file->getClientOriginalName(),
                                'size' => $file->getSize(),
                                'mime_type' => $file->getMimeType(),
                            ];
                        }
                        break;

                    case 'checkbox':
                        // Múltipla escolha - salva como JSON
                        $answerData['json_value'] = $value;
                        $answerData['value'] = implode(', ', $value);
                        break;

                    case 'number':
                        $answerData['value'] = $value;
                        $answerData['number_value'] = $value;
                        break;

                    case 'date':
                        $answerData['value'] = $value;
                        $answerData['date_value'] = $value;
                        break;

                    case 'datetime':
                        $answerData['value'] = $value;
                        $answerData['datetime_value'] = $value;
                        break;

                    default:
                        // Campos de texto simples
                        $answerData['value'] = $value;
                        break;
                }

                FormAnswer::create($answerData);
            }

            DB::commit();

            return redirect()
                ->route('form.success', ['slug' => $slug])
                ->with('success', 'Formulário enviado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao enviar o formulário. Por favor, tente novamente.']);
        }
    }

    /**
     * Página de sucesso após submissão
     */
    public function success(string $slug)
    {
        $form = Form::where('slug', $slug)->firstOrFail();

        return view('forms.success', [
            'form' => $form,
        ]);
    }
}
