<?php

namespace App\Filament\Resources\FormSubmissions\Pages;

use App\Filament\Resources\FormSubmissions\FormSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components;
use Filament\Forms\Components as FormComponents;

class ViewFormSubmission extends ViewRecord
{
    protected static string $resource = FormSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_form')
                ->label('Ver Formulário Original')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->url(fn () => route('form.show', $this->record->form->slug))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        $submission = $this->record;
        $form = $submission->form;

        $sections = [];

        // Seção de Informações da Submissão
        $sections[] = Components\Section::make('Informações da Submissão')
            ->schema([
                Components\Grid::make(3)->schema([
                    FormComponents\Placeholder::make('id')
                        ->label('ID da Resposta')
                        ->content($submission->id),

                    FormComponents\Placeholder::make('created_at')
                        ->label('Data de Envio')
                        ->content($submission->created_at->format('d/m/Y H:i:s')),

                    FormComponents\Placeholder::make('ip')
                        ->label('Endereço IP')
                        ->content($submission->ip ?? 'Não registrado'),
                ]),

                Components\Grid::make(2)->schema([
                    FormComponents\Placeholder::make('form_title')
                        ->label('Formulário')
                        ->content($form->title),

                    FormComponents\Placeholder::make('audience')
                        ->label('Público-alvo')
                        ->content($form->audience === 'company' ? 'Empresa' : 'Vistoriador'),
                ]),

                FormComponents\Placeholder::make('user_agent')
                    ->label('Navegador / Dispositivo')
                    ->content($submission->user_agent ?? 'Não informado')
                    ->columnSpanFull(),
            ])
            ->collapsible();

        // Agrupa respostas por seção
        $answersBySection = $submission->answers()
            ->with('field.section')
            ->get()
            ->groupBy(fn ($answer) => $answer->field->section_id);

        // Campos sem seção
        if ($answersBySection->has(null)) {
            $sections[] = $this->buildSectionSchema('Campos Gerais', $answersBySection->get(null));
        }

        // Campos com seção
        foreach ($form->sections()->orderBy('position')->get() as $section) {
            if ($answersBySection->has($section->id)) {
                $sections[] = $this->buildSectionSchema(
                    $section->title,
                    $answersBySection->get($section->id),
                    $section->description
                );
            }
        }

        return $schema->schema($sections);
    }

    protected function buildSectionSchema(string $title, $answers, ?string $description = null): Components\Section
    {
        $fields = [];

        foreach ($answers->sortBy('field.position') as $answer) {
            $field = $answer->field;
            $value = $this->formatAnswerValue($answer);

            $placeholder = FormComponents\Placeholder::make("answer_{$answer->id}")
                ->label($field->label . ($field->required ? ' *' : ''))
                ->content($value);

            $fields[] = $placeholder;
        }

        $section = Components\Section::make($title)
            ->schema($fields)
            ->columns(2)
            ->collapsible();

        if ($description) {
            $section->description($description);
        }

        return $section;
    }

    protected function formatAnswerValue($answer): string
    {
        $field = $answer->field;

        // Arquivo
        if ($field->type === 'file' && $answer->json_value) {
            $fileName = $answer->json_value['original_name'] ?? 'Arquivo enviado';
            $filePath = asset('storage/' . $answer->value);
            return "📎 <a href='{$filePath}' target='_blank' class='text-primary-600 hover:underline'>{$fileName}</a>";
        }

        // Múltipla escolha
        if ($field->type === 'checkbox' && is_array($answer->json_value)) {
            return implode(', ', $answer->json_value);
        }

        // Data
        if ($field->type === 'date' && $answer->date_value) {
            return $answer->date_value->format('d/m/Y');
        }

        // Data/Hora
        if ($field->type === 'datetime' && $answer->datetime_value) {
            return $answer->datetime_value->format('d/m/Y H:i');
        }

        // Número
        if ($field->type === 'number' && $answer->number_value) {
            return number_format($answer->number_value, 2, ',', '.');
        }

        // Rating
        if ($field->type === 'rating') {
            $stars = str_repeat('⭐', (int)$answer->value);
            return $answer->value . ' ' . $stars;
        }

        // Padrão
        return $answer->value ?? 'Não informado';
    }
}
