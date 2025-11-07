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

                SchemaComponents\Grid::make(2)
                    ->schema([
                        Components\Select::make('form_section_id')
                            ->label('Seção (Opcional)')
                            ->options(function (RelationManager $livewire) {
                                return FormSection::where('form_id', $livewire->getOwnerRecord()->id)
                                    ->pluck('title', 'id');
                            })
                            ->searchable()
                            ->nullable()
                            ->createOptionForm([
                                Components\TextInput::make('title')
                                    ->label('Título da Seção')
                                    ->required(),
                                Components\Textarea::make('description')
                                    ->label('Descrição')
                                    ->rows(2),
                                Components\Hidden::make('form_id')
                                    ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->id),
                            ])
                            ->createOptionUsing(function (array $data, RelationManager $livewire) {
                                $data['form_id'] = $livewire->getOwnerRecord()->id;
                                $data['position'] = FormSection::where('form_id', $data['form_id'])->max('position') + 1;
                                return FormSection::create($data)->id;
                            }),

                        Components\TextInput::make('position')
                            ->label('Ordem')
                            ->numeric()
                            ->default(fn (RelationManager $livewire) =>
                                $livewire->getOwnerRecord()->fields()->max('position') + 1
                            )
                            ->required(),
                    ]),

                Components\TextInput::make('label')
                    ->label('Rótulo do Campo')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                        if (!$get('name')) {
                            $set('name', str($state)->slug('_')->toString());
                        }
                    })
                    ->helperText('Texto que o respondente verá'),

                Components\TextInput::make('name')
                    ->label('Nome Técnico')
                    ->required()
                    ->alphaDash()
                    ->helperText('Identificador único (gerado automaticamente)'),

                Components\Hidden::make('type')
                    ->default('text'),

                Components\Toggle::make('required')
                    ->label('Campo Obrigatório')
                    ->default(false)
                    ->inline(false),

                Components\Toggle::make('active')
                    ->label('Campo Ativo')
                    ->default(true)
                    ->inline(false),

                // Repeater para opções (apenas para select, radio, checkbox)
                Components\Repeater::make('options')
                    ->label('Opções de Escolha')
                    ->schema([
                        Components\TextInput::make('label')
                            ->label('Rótulo')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (!$get('value')) {
                                    $set('value', str($state)->slug('_')->toString());
                                }
                            }),
                        Components\TextInput::make('value')
                            ->label('Valor')
                            ->required()
                            ->alphaDash(),
                    ])
                    ->columns(2)
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                    ->addActionLabel('Adicionar Opção')
                    ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'checkbox']))
                    ->defaultItems(0),

                Components\KeyValue::make('validation')
                    ->label('Validações Avançadas (Opcional)')
                    ->addActionLabel('Adicionar Regra')
                    ->keyLabel('Regra')
                    ->valueLabel('Valor')
                    ->helperText('Para usuários avançados: min, max, regex, etc.')
                    ->nullable(),

                Components\KeyValue::make('ui')
                    ->label('Configurações de Interface (Opcional)')
                    ->addActionLabel('Adicionar Config')
                    ->keyLabel('Propriedade')
                    ->valueLabel('Valor')
                    ->helperText('Para usuários avançados: placeholder, help_text, etc.')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('section.title')
                    ->label('Seção')
                    ->placeholder('Sem seção')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('label')
                    ->label('Campo')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->name),

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
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'file' => 'warning',
                        'select', 'radio', 'checkbox' => 'info',
                        'date', 'datetime' => 'success',
                        'email', 'tel' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('options_count')
                    ->label('Opções')
                    ->counts('options')
                    ->badge()
                    ->color('info')
                    ->visible(fn ($record) => $record && in_array($record->type, ['select', 'radio', 'checkbox'])),

                Tables\Columns\IconColumn::make('required')
                    ->label('Obrigatório')
                    ->boolean(),

                Tables\Columns\IconColumn::make('active')
                    ->label('Ativo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('position')
                    ->label('Ordem')
                    ->sortable(),
            ])
            ->defaultSort('position')
            ->filters([
                Tables\Filters\SelectFilter::make('form_section_id')
                    ->label('Seção')
                    ->options(fn (RelationManager $livewire) =>
                        FormSection::where('form_id', $livewire->getOwnerRecord()->id)
                            ->pluck('title', 'id')
                    ),

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
                    ->using(function (array $data, RelationManager $livewire): mixed {
                        $data['form_id'] = $livewire->getOwnerRecord()->id;

                        // Salvar opções separadamente
                        $options = $data['options'] ?? [];
                        unset($data['options'], $data['template']);

                        // Criar o campo
                        $field = $livewire->getOwnerRecord()->fields()->create($data);

                        // Criar opções se existirem
                        if (!empty($options)) {
                            foreach ($options as $index => $option) {
                                $option['position'] = $index + 1;
                                $field->options()->create($option);
                            }
                        }

                        return $field;
                    }),
            ])
            ->recordActions([
                EditAction::make()
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
                                $option['position'] = $index + 1;
                                $record->options()->create($option);
                            }
                        }

                        return $record;
                    }),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('position')
            ->emptyStateHeading('Nenhum campo adicionado')
            ->emptyStateDescription('Comece adicionando campos ao seu formulário usando os templates prontos!')
            ->emptyStateIcon('heroicon-o-squares-plus');
    }
}
