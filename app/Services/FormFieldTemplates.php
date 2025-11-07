<?php

namespace App\Services;

class FormFieldTemplates
{
    public static function getTemplates(): array
    {
        return [
            'common' => [
                'label' => 'Campos Comuns',
                'fields' => [
                    'cnpj' => [
                        'label' => 'CNPJ',
                        'icon' => 'heroicon-o-building-office',
                        'config' => [
                            'label' => 'CNPJ',
                            'name' => 'cnpj',
                            'type' => 'text',
                            'required' => true,
                            'validation' => [
                                'regex' => '/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/',
                            ],
                            'ui' => [
                                'mask' => '99.999.999/9999-99',
                                'placeholder' => '00.000.000/0000-00',
                            ],
                        ],
                    ],
                    'cpf' => [
                        'label' => 'CPF',
                        'icon' => 'heroicon-o-identification',
                        'config' => [
                            'label' => 'CPF',
                            'name' => 'cpf',
                            'type' => 'text',
                            'required' => true,
                            'validation' => [
                                'regex' => '/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
                            ],
                            'ui' => [
                                'mask' => '999.999.999-99',
                                'placeholder' => '000.000.000-00',
                            ],
                        ],
                    ],
                    'phone' => [
                        'label' => 'Telefone',
                        'icon' => 'heroicon-o-phone',
                        'config' => [
                            'label' => 'Telefone',
                            'name' => 'phone',
                            'type' => 'tel',
                            'required' => false,
                            'ui' => [
                                'mask' => '(99) 99999-9999',
                                'placeholder' => '(00) 00000-0000',
                            ],
                        ],
                    ],
                    'cep' => [
                        'label' => 'CEP',
                        'icon' => 'heroicon-o-map-pin',
                        'config' => [
                            'label' => 'CEP',
                            'name' => 'cep',
                            'type' => 'text',
                            'required' => false,
                            'ui' => [
                                'mask' => '99999-999',
                                'placeholder' => '00000-000',
                            ],
                        ],
                    ],
                    'email' => [
                        'label' => 'E-mail',
                        'icon' => 'heroicon-o-envelope',
                        'config' => [
                            'label' => 'E-mail',
                            'name' => 'email',
                            'type' => 'email',
                            'required' => true,
                            'ui' => [
                                'placeholder' => 'exemplo@email.com',
                            ],
                        ],
                    ],
                    'currency' => [
                        'label' => 'Valor em Reais',
                        'icon' => 'heroicon-o-currency-dollar',
                        'config' => [
                            'label' => 'Valor',
                            'name' => 'value',
                            'type' => 'number',
                            'required' => false,
                            'validation' => [
                                'min' => 0,
                                'step' => 0.01,
                            ],
                            'ui' => [
                                'prefix' => 'R$ ',
                                'decimals' => 2,
                            ],
                        ],
                    ],
                    'date' => [
                        'label' => 'Data',
                        'icon' => 'heroicon-o-calendar',
                        'config' => [
                            'label' => 'Data',
                            'name' => 'date',
                            'type' => 'date',
                            'required' => false,
                        ],
                    ],
                    'yes_no' => [
                        'label' => 'Sim/Não',
                        'icon' => 'heroicon-o-check-circle',
                        'config' => [
                            'label' => 'Confirmação',
                            'name' => 'confirmation',
                            'type' => 'radio',
                            'required' => true,
                            'options' => [
                                ['label' => 'Sim', 'value' => 'yes', 'position' => 1],
                                ['label' => 'Não', 'value' => 'no', 'position' => 2],
                            ],
                        ],
                    ],
                    'pdf' => [
                        'label' => 'Upload de PDF',
                        'icon' => 'heroicon-o-document-text',
                        'config' => [
                            'label' => 'Enviar PDF',
                            'name' => 'pdf_file',
                            'type' => 'file',
                            'required' => false,
                            'validation' => [
                                'extensions' => ['pdf'],
                                'max_size' => 5120, // 5MB
                            ],
                            'ui' => [
                                'help_text' => 'Envie um arquivo PDF (máx 5MB)',
                            ],
                        ],
                    ],
                    'excel' => [
                        'label' => 'Upload de Excel',
                        'icon' => 'heroicon-o-table-cells',
                        'config' => [
                            'label' => 'Enviar Planilha',
                            'name' => 'excel_file',
                            'type' => 'file',
                            'required' => false,
                            'validation' => [
                                'extensions' => ['xlsx', 'xls', 'csv'],
                                'max_size' => 10240, // 10MB
                            ],
                            'ui' => [
                                'help_text' => 'Envie uma planilha Excel ou CSV (máx 10MB)',
                            ],
                        ],
                    ],
                    'image' => [
                        'label' => 'Upload de Imagem',
                        'icon' => 'heroicon-o-photo',
                        'config' => [
                            'label' => 'Enviar Imagem',
                            'name' => 'image',
                            'type' => 'file',
                            'required' => false,
                            'validation' => [
                                'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
                                'max_size' => 5120, // 5MB
                            ],
                            'ui' => [
                                'help_text' => 'Envie uma imagem JPG, PNG ou WebP (máx 5MB)',
                                'image_preview' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'custom' => [
                'label' => 'Campos Personalizados',
                'fields' => [
                    'text' => [
                        'label' => 'Texto Curto',
                        'icon' => 'heroicon-o-bars-3-bottom-left',
                        'config' => [
                            'label' => 'Campo de Texto',
                            'name' => 'text_field',
                            'type' => 'text',
                            'required' => false,
                        ],
                    ],
                    'textarea' => [
                        'label' => 'Texto Longo',
                        'icon' => 'heroicon-o-document',
                        'config' => [
                            'label' => 'Texto Longo',
                            'name' => 'textarea_field',
                            'type' => 'textarea',
                            'required' => false,
                        ],
                    ],
                    'number' => [
                        'label' => 'Número',
                        'icon' => 'heroicon-o-hashtag',
                        'config' => [
                            'label' => 'Número',
                            'name' => 'number_field',
                            'type' => 'number',
                            'required' => false,
                        ],
                    ],
                    'select' => [
                        'label' => 'Lista de Opções',
                        'icon' => 'heroicon-o-list-bullet',
                        'config' => [
                            'label' => 'Selecione uma opção',
                            'name' => 'select_field',
                            'type' => 'select',
                            'required' => false,
                            'options' => [],
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function getTemplate(string $category, string $key): ?array
    {
        $templates = self::getTemplates();
        return $templates[$category]['fields'][$key] ?? null;
    }
}
