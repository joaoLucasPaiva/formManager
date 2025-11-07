@extends('layouts.form')

@section('title', $form->title)

@section('content')
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        {{-- Cabeçalho do Formulário --}}
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-12 text-white">
            <h1 class="text-4xl font-bold mb-3">{{ $form->title }}</h1>
            @if($form->description)
                <p class="text-indigo-100 text-lg leading-relaxed">{{ $form->description }}</p>
            @endif
            <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ $form->audience === 'company' ? 'Empresa' : 'Vistoriador' }}
            </div>
        </div>

        {{-- Formulário --}}
        <div class="px-8 py-10">
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-red-800 font-semibold mb-2">Erro ao enviar formulário</h3>
                            <ul class="list-disc list-inside text-red-700 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('form.submit', $form->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- Campos sem seção --}}
                @if($fieldsBySection->has(null))
                    <div class="space-y-6">
                        @foreach($fieldsBySection->get(null) as $field)
                            @include('forms.fields.' . $field->type, ['field' => $field])
                        @endforeach
                    </div>
                @endif

                {{-- Campos organizados por seções --}}
                @foreach($sections as $section)
                    @if($fieldsBySection->has($section->id))
                        <div class="border-t border-gray-200 pt-8">
                            <div class="mb-6">
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $section->title }}</h2>
                                @if($section->description)
                                    <p class="text-gray-600">{{ $section->description }}</p>
                                @endif
                            </div>

                            <div class="space-y-6">
                                @foreach($fieldsBySection->get($section->id) as $field)
                                    @include('forms.fields.' . $field->type, ['field' => $field])
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Botão de envio --}}
                <div class="border-t border-gray-200 pt-8 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        <span class="text-red-500">*</span> Campos obrigatórios
                    </p>
                    <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Enviar Formulário
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Funções de máscara
            const masks = {
                cpf: function(value) {
                    value = value.replace(/\D/g, '').substring(0, 11); // Limita a 11 dígitos
                    if (value.length <= 3) return value;
                    if (value.length <= 6) return value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
                    if (value.length <= 9) return value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
                    return value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
                },
                cnpj: function(value) {
                    value = value.replace(/\D/g, '').substring(0, 14); // Limita a 14 dígitos
                    if (value.length <= 2) return value;
                    if (value.length <= 5) return value.replace(/(\d{2})(\d{1,3})/, '$1.$2');
                    if (value.length <= 8) return value.replace(/(\d{2})(\d{3})(\d{1,3})/, '$1.$2.$3');
                    if (value.length <= 12) return value.replace(/(\d{2})(\d{3})(\d{3})(\d{1,4})/, '$1.$2.$3/$4');
                    return value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{1,2})/, '$1.$2.$3/$4-$5');
                },
                phone: function(value) {
                    value = value.replace(/\D/g, '').substring(0, 11); // Limita a 11 dígitos
                    if (value.length <= 2) return value;
                    if (value.length <= 6) {
                        // Formato (00) 0000
                        return value.replace(/(\d{2})(\d{1,4})/, '($1) $2');
                    }
                    if (value.length <= 10) {
                        // Formato (00) 0000-0000
                        return value.replace(/(\d{2})(\d{4})(\d{1,4})/, '($1) $2-$3');
                    }
                    // Formato (00) 00000-0000
                    return value.replace(/(\d{2})(\d{5})(\d{1,4})/, '($1) $2-$3');
                },
                cep: function(value) {
                    value = value.replace(/\D/g, '').substring(0, 8); // Limita a 8 dígitos
                    if (value.length <= 5) return value;
                    return value.replace(/(\d{5})(\d{1,3})/, '$1-$2');
                }
            };

            // Detecta campos que precisam de máscara
            function applyMasks() {
                // Por nome do campo
                const fieldMappings = {
                    'cpf': 'cpf',
                    'cnpj': 'cnpj',
                    'phone': 'phone',
                    'telefone': 'phone',
                    'cep': 'cep'
                };

                document.querySelectorAll('input[type="text"], input[type="tel"]').forEach(input => {
                    const fieldName = (input.dataset.fieldName || '').toLowerCase();
                    const placeholder = (input.placeholder || '').toLowerCase();

                    // Detecta o tipo de máscara necessário
                    let maskType = null;

                    // Por nome do campo
                    for (const [key, mask] of Object.entries(fieldMappings)) {
                        if (fieldName.includes(key)) {
                            maskType = mask;
                            break;
                        }
                    }

                    // Por placeholder se não encontrou pelo nome
                    if (!maskType) {
                        if (placeholder.includes('cpf')) maskType = 'cpf';
                        else if (placeholder.includes('cnpj')) maskType = 'cnpj';
                        else if (placeholder.includes('telefone') || placeholder.includes('phone')) maskType = 'phone';
                        else if (placeholder.includes('cep')) maskType = 'cep';
                    }

                    // Aplica a máscara
                    if (maskType && masks[maskType]) {
                        input.addEventListener('input', function(e) {
                            let cursorPosition = e.target.selectionStart;
                            const oldValue = e.target.value;
                            const oldLength = oldValue.length;

                            const masked = masks[maskType](oldValue);
                            e.target.value = masked;

                            // Ajusta a posição do cursor
                            const newLength = masked.length;

                            // Se o cursor estava no final, mantém no final
                            if (cursorPosition >= oldLength) {
                                cursorPosition = newLength;
                            } else {
                                // Caso contrário, ajusta proporcionalmente
                                cursorPosition = Math.min(cursorPosition + (newLength - oldLength), newLength);
                            }

                            e.target.setSelectionRange(cursorPosition, cursorPosition);
                        });

                        // Aplica máscara no valor inicial (caso tenha old() do Laravel)
                        if (input.value) {
                            input.value = masks[maskType](input.value);
                        }
                    }
                });
            }

            // Aplica as máscaras quando a página carrega
            applyMasks();
        });
    </script>
    @endpush
@endsection
