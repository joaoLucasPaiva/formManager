<?php

namespace App\Filament\Resources\FormFields;

use App\Filament\Resources\FormFields\Pages\ManageFormFields;
use App\Models\FormField;
use App\Models\Form;
use App\Models\FormSection;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FormFieldResource extends Resource
{
    protected static ?string $model = FormField::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-plus';
    protected static string|\UnitEnum|null $navigationGroup = 'Form Builder';
    protected static ?string $navigationLabel = 'Campos';
    protected static ?string $modelLabel = 'Campo';
    protected static ?string $pluralModelLabel = 'Campos';

    // Ocultar do menu - campos são gerenciados via RelationManager no FormResource
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Select::make('form_id')
                    ->label('Formulário')
                    ->options(Form::pluck('title', 'id'))
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('form_section_id', null)),

                Components\Select::make('form_section_id')
                    ->label('Seção (Opcional)')
                    ->options(function (callable $get) {
                        $formId = $get('form_id');
                        if (!$formId) return [];
                        return FormSection::where('form_id', $formId)->pluck('title', 'id');
                    })
                    ->searchable()
                    ->nullable(),

                Components\TextInput::make('label')
                    ->label('Rótulo')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Texto exibido ao respondente'),

                Components\TextInput::make('name')
                    ->label('Nome (identificador)')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Ex: company_cnpj, employees_count')
                    ->alphaDash(),

                Components\Select::make('type')
                    ->label('Tipo de Campo')
                    ->required()
                    ->options([
                        'text' => 'Texto',
                        'textarea' => 'Texto Longo',
                        'number' => 'Número',
                        'select' => 'Seleção Única (Dropdown)',
                        'radio' => 'Seleção Única (Radio)',
                        'checkbox' => 'Múltipla Escolha',
                        'date' => 'Data',
                        'datetime' => 'Data e Hora',
                        'file' => 'Arquivo',
                        'email' => 'E-mail',
                        'tel' => 'Telefone',
                        'rating' => 'Avaliação',
                    ])
                    ->reactive(),

                Components\Toggle::make('required')
                    ->label('Obrigatório')
                    ->default(false),

                Components\Toggle::make('active')
                    ->label('Ativo')
                    ->default(true),

                Components\TextInput::make('position')
                    ->label('Posição')
                    ->numeric()
                    ->default(1)
                    ->required(),

                Components\KeyValue::make('validation')
                    ->label('Regras de Validação')
                    ->addActionLabel('Adicionar Regra')
                    ->keyLabel('Regra')
                    ->valueLabel('Valor')
                    ->helperText('Ex: min => 10, max => 100, regex => /^\d+$/')
                    ->nullable(),

                Components\KeyValue::make('ui')
                    ->label('Configurações de Interface')
                    ->addActionLabel('Adicionar Config')
                    ->keyLabel('Propriedade')
                    ->valueLabel('Valor')
                    ->helperText('Ex: placeholder => Digite aqui, mask => 99.999.999/9999-99')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('form.title')
                    ->label('Formulário')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('section.title')
                    ->label('Seção')
                    ->placeholder('Sem seção')
                    ->sortable(),

                Tables\Columns\TextColumn::make('label')
                    ->label('Rótulo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'file' => 'warning',
                        'select', 'radio', 'checkbox' => 'info',
                        'date', 'datetime' => 'success',
                        default => 'gray',
                    }),

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
                Tables\Filters\SelectFilter::make('form_id')
                    ->label('Formulário')
                    ->options(Form::pluck('title', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'text' => 'Texto',
                        'textarea' => 'Texto Longo',
                        'number' => 'Número',
                        'select' => 'Select',
                        'radio' => 'Radio',
                        'checkbox' => 'Checkbox',
                        'date' => 'Data',
                        'datetime' => 'Data/Hora',
                        'file' => 'Arquivo',
                        'email' => 'E-mail',
                        'tel' => 'Telefone',
                    ]),

                Tables\Filters\TernaryFilter::make('required')
                    ->label('Obrigatório'),

                Tables\Filters\TernaryFilter::make('active')
                    ->label('Ativo'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('position');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFormFields::route('/'),
        ];
    }
}
