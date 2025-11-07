<?php

namespace App\Filament\Resources\Forms\RelationManagers;

use App\Models\FormSection;
use App\Services\FormFieldTemplates;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables;
use Filament\Tables\Table;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    protected static ?string $title = 'Campos do Formulário';
    protected static string|\BackedEnum|null $icon = 'heroicon-o-squares-plus';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Seletor de Template
                Components\Select::make('template')
                    ->label('Tipo de Campo')
                    ->options(function () {
                        $templates = FormFieldTemplates::getTemplates();
                        $options = [];

                        foreach ($templates as $category => $data) {
                            foreach ($data['fields'] as $key => $field) {
                                $options[$data['label']][$category . '.' . $key] = $field['label'];
                            }
                        }

                        return $options;
                    })
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if (!$state) {
                            return;
                        }

                        [$category, $key] = explode('.', $state);
                        $template = FormFieldTemplates::getTemplate($category, $key);

                        if ($template && isset($template['config'])) {
                            $config = $template['config'];

                            $set('label', $config['label']);
                            $set('name', $config['name']);
                            $set('type', $config['type']);
                            $set('required', $config['required'] ?? false);
                            $set('validation', $config['validation'] ?? null);
                            $set('ui', $config['ui'] ?? null);

                            // Se tem opções pré-definidas (como Sim/Não)
                            if (isset($config['options'])) {
                                $set('options', $config['options']);
                            }
                        }
                    })
                    ->helperText('Escolha um campo pronto ou personalize abaixo'),

                Components\Select::make('form_section_id')
                    ->label('Seção (Opcional)')
                    ->options(function (RelationManager $livewire) {
                        return FormSection::where('form_id', $livewire->getOwnerRecord()->id)
                            ->orderBy('position')
                            ->pluck('title', 'id');
                    })
                    ->searchable()
                    ->nullable()
                    ->helperText('Agrupe campos relacionados em seções')
                    ->createOptionForm([
                        Components\TextInput::make('title')
                            ->label('Título da Seção')
                            ->required()
                            ->placeholder('Ex: Dados da Empresa'),
                        Components\Textarea::make('description')
                            ->label('Descrição (Opcional)')
                            ->rows(2)
                            ->placeholder('Descreva o que deve ser preenchido nesta seção'),
                        Components\Hidden::make('form_id')
                            ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->id),
                    ])
                    ->createOptionUsing(function (array $data, RelationManager $livewire) {
                        $data['form_id'] = $livewire->getOwnerRecord()->id;
                        $data['position'] = FormSection::where('form_id', $data['form_id'])->max('position') + 1;
                        return FormSection::create($data)->id;
                    }),

                Components\Hidden::make('position')
                    ->default(fn (RelationManager $livewire, $record) =>
                        $record?->position ?? ($livewire->getOwnerRecord()->fields()->max('position') ?? 0) + 1
                    ),

                Components\TextInput::make('label')
                    ->label('Rótulo do Campo')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                        if (!$get('name')) {
                            $set('name', str($state)->slug('_')->toString());
                        }
                    })
                    ->helperText('Texto que será exibido no formulário'),

                Components\Hidden::make('name')
                    ->default(fn (Get $get) => $get('name') ?? ''),

                Components\Hidden::make('type')
                    ->default('text'),

                SchemaComponents\Grid::make(3)
                    ->schema([
                        Components\Toggle::make('required')
                            ->label('Campo Obrigatório?')
                            ->default(false)
                            ->inline(false)
                            ->helperText('Respondente precisa preencher'),

                        Components\Toggle::make('active')
                            ->label('Campo Ativo?')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Mostrar no formulário'),

                        Components\Toggle::make('is_identifier')
                            ->label('Campo Identificador?')
                            ->default(false)
                            ->inline(false)
                            ->helperText('Apenas 1 campo pode ser identificador')
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?bool $state, RelationManager $livewire, $record) {
                                // Se marcar como identificador
                                if ($state) {
                                    // Marca como obrigatório também
                                    $set('required', true);

                                    // Desmarca todos os outros campos deste formulário como identificador
                                    $formId = $livewire->getOwnerRecord()->id;
                                    \App\Models\FormField::where('form_id', $formId)
                                        ->where('id', '!=', $record?->id ?? 0)
                                        ->where('is_identifier', true)
                                        ->update(['is_identifier' => false]);
                                }
                            })
                            ->afterStateHydrated(function (Components\Toggle $component, RelationManager $livewire, $record) {
                                // Mostra alerta se já existe outro campo identificador
                                if ($record && !$record->is_identifier) {
                                    $formId = $livewire->getOwnerRecord()->id;
                                    $hasIdentifier = \App\Models\FormField::where('form_id', $formId)
                                        ->where('is_identifier', true)
                                        ->exists();

                                    if ($hasIdentifier) {
                                        $component->helperText('⚠️ Já existe um campo identificador. Marcar este desmarcará o outro.');
                                    }
                                }
                            }),
                    ]),

                // Repeater para opções (apenas para select, radio, checkbox)
                Components\Repeater::make('options')
                    ->label('Opções de Escolha')
                    ->schema([
                        Components\TextInput::make('label')
                            ->label('Nome da Opção')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (!$get('value')) {
                                    $set('value', str($state)->slug('_')->toString());
                                }
                            })
                            ->placeholder('Ex: Microempresa'),
                        Components\Hidden::make('value')
                            ->default(fn (Get $get) => $get('value') ?? ''),
                    ])
                    ->reorderable()
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                    ->addActionLabel('+ Adicionar Opção')
                    ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'checkbox']))
                    ->defaultItems(0)
                    ->helperText('Adicione as opções que o usuário poderá escolher'),

                Components\Hidden::make('validation'),
                Components\Hidden::make('ui'),
            ]);
    }

    public function table(Table $table): Table
    {
        $isLocked = $this->getOwnerRecord()->is_locked;

        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('position')
                    ->label('#')
                    ->sortable()
                    ->width(60)
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('label')
                    ->label('Nome do Campo')
                    ->searchable()
                    ->description(fn ($record) => $record->section ? '📁 ' . $record->section->title : null)
                    ->icon('heroicon-o-bars-3')
                    ->iconColor('gray'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'text' => 'Texto',
                        'textarea' => 'Texto Longo',
                        'number' => 'Número',
                        'select' => 'Lista',
                        'radio' => 'Escolha Única',
                        'checkbox' => 'Múltipla Escolha',
                        'date' => 'Data',
                        'datetime' => 'Data/Hora',
                        'file' => 'Arquivo',
                        'email' => 'E-mail',
                        'tel' => 'Telefone',
                        'rating' => 'Avaliação',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'file' => 'warning',
                        'select', 'radio', 'checkbox' => 'info',
                        'date', 'datetime' => 'success',
                        'email', 'tel' => 'primary',
                        'rating' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('options_count')
                    ->label('Opções')
                    ->counts('options')
                    ->badge()
                    ->color('info')
                    ->visible(fn ($record) => $record && in_array($record->type, ['select', 'radio', 'checkbox']))
                    ->toggleable(),

                Tables\Columns\IconColumn::make('required')
                    ->label('Obrigatório')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_identifier')
                    ->label('Identificador')
                    ->boolean()
                    ->alignCenter()
                    ->tooltip('Campo usado para identificar quem respondeu'),

                Tables\Columns\IconColumn::make('active')
                    ->label('Ativo')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),
            ])
            ->defaultSort('position', 'asc')
            ->defaultGroup('section.title')
            ->groups([
                Tables\Grouping\Group::make('section.title')
                    ->label('Seção')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('form_section_id')
                    ->label('Filtrar por Seção')
                    ->options(fn (RelationManager $livewire) =>
                        FormSection::where('form_id', $livewire->getOwnerRecord()->id)
                            ->orderBy('position')
                            ->pluck('title', 'id')
                    )
                    ->placeholder('Todas as seções'),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'text' => 'Texto',
                        'textarea' => 'Texto Longo',
                        'number' => 'Número',
                        'select' => 'Lista',
                        'date' => 'Data',
                        'file' => 'Arquivo',
                        'email' => 'E-mail',
                        'tel' => 'Telefone',
                    ]),

                Tables\Filters\TernaryFilter::make('required')
                    ->label('Obrigatório'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->disabled(fn () => $isLocked)
                    ->tooltip(fn () => $isLocked ? 'Formulário publicado não pode ser editado' : null)
                    ->using(function (array $data, RelationManager $livewire): mixed {
                        $data['form_id'] = $livewire->getOwnerRecord()->id;

                        // Gera o name automaticamente se não existir
                        if (empty($data['name']) && !empty($data['label'])) {
                            $data['name'] = str($data['label'])->slug('_')->toString();
                        }

                        // Salvar opções separadamente
                        $options = $data['options'] ?? [];
                        unset($data['options'], $data['template']);

                        // Criar o campo
                        $field = $livewire->getOwnerRecord()->fields()->create($data);

                        // Criar opções se existirem
                        if (!empty($options)) {
                            foreach ($options as $index => $option) {
                                // Gera value se estiver vazio
                                if (empty($option['value']) && !empty($option['label'])) {
                                    $option['value'] = str($option['label'])->slug('_')->toString();
                                }
                                $option['position'] = $index + 1;
                                $field->options()->create($option);
                            }
                        }

                        return $field;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->disabled(fn () => $isLocked)
                    ->tooltip(fn () => $isLocked ? 'Formulário publicado não pode ser editado' : null)
                    ->fillForm(function ($record): array {
                        // Carregar opções existentes
                        $data = $record->toArray();
                        $data['options'] = $record->options()
                            ->orderBy('position')
                            ->get()
                            ->map(fn ($opt) => [
                                'label' => $opt->label,
                                'value' => $opt->value,
                                'position' => $opt->position,
                            ])
                            ->toArray();

                        return $data;
                    })
                    ->using(function ($record, array $data): mixed {
                        // Preservar validações e UI existentes se não foram enviadas
                        if (!isset($data['validation'])) {
                            $data['validation'] = $record->validation;
                        }
                        if (!isset($data['ui'])) {
                            $data['ui'] = $record->ui;
                        }
                        if (!isset($data['name']) || empty($data['name'])) {
                            $data['name'] = $record->name;
                        }

                        // Salvar opções separadamente
                        $options = $data['options'] ?? [];
                        unset($data['options'], $data['template']);

                        // Atualizar o campo
                        $record->update($data);

                        // Atualizar opções
                        if (isset($options)) {
                            // Deletar opções antigas
                            $record->options()->delete();

                            // Criar novas
                            foreach ($options as $index => $option) {
                                // Gera value se estiver vazio
                                if (empty($option['value']) && !empty($option['label'])) {
                                    $option['value'] = str($option['label'])->slug('_')->toString();
                                }
                                $option['position'] = $index + 1;
                                $record->options()->create($option);
                            }
                        }

                        return $record;
                    }),

                DeleteAction::make()
                    ->disabled(fn () => $isLocked)
                    ->tooltip(fn () => $isLocked ? 'Formulário publicado não pode ser editado' : null),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->disabled(fn () => $isLocked)
                        ->tooltip(fn () => $isLocked ? 'Formulário publicado não pode ser editado' : null),
                ]),
            ])
            ->reorderable($isLocked ? null : 'position')
            ->defaultSort('position', 'asc')
            ->emptyStateHeading('Nenhum campo adicionado')
            ->emptyStateDescription('Comece adicionando campos ao seu formulário usando os templates prontos!')
            ->emptyStateIcon('heroicon-o-squares-plus')
            ->heading(fn () => $isLocked ? 'Campos do Formulário (somente leitura)' : 'Campos do Formulário - Arraste para reordenar ↕️');
    }
}
