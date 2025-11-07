<?php

namespace App\Filament\Resources\FormFieldOptions;

use App\Filament\Resources\FormFieldOptions\Pages\ManageFormFieldOptions;
use App\Models\FormFieldOption;
use App\Models\FormField;
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

class FormFieldOptionResource extends Resource
{
    protected static ?string $model = FormFieldOption::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';
    protected static string|\UnitEnum|null $navigationGroup = 'Form Builder';
    protected static ?string $navigationLabel = 'Opções de Campos';
    protected static ?string $modelLabel = 'Opção';
    protected static ?string $pluralModelLabel = 'Opções';

    // Ocultar do menu - opções são gerenciadas inline ao criar campos
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Select::make('form_field_id')
                    ->label('Campo')
                    ->options(FormField::whereIn('type', ['select', 'radio', 'checkbox'])
                        ->with('form')
                        ->get()
                        ->mapWithKeys(fn($field) => [
                            $field->id => "{$field->form->title} → {$field->label}"
                        ]))
                    ->required()
                    ->searchable(),

                Components\TextInput::make('label')
                    ->label('Rótulo da Opção')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Texto exibido ao respondente'),

                Components\TextInput::make('value')
                    ->label('Valor')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Valor enviado no formulário')
                    ->alphaDash(),

                Components\TextInput::make('position')
                    ->label('Posição')
                    ->numeric()
                    ->default(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('field.form.title')
                    ->label('Formulário')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('field.label')
                    ->label('Campo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('label')
                    ->label('Rótulo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Valor')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('position')
                    ->label('Ordem')
                    ->sortable(),
            ])
            ->defaultSort('position')
            ->filters([
                Tables\Filters\SelectFilter::make('form_field_id')
                    ->label('Campo')
                    ->options(FormField::whereIn('type', ['select', 'radio', 'checkbox'])
                        ->with('form')
                        ->get()
                        ->mapWithKeys(fn($field) => [
                            $field->id => "{$field->form->title} → {$field->label}"
                        ]))
                    ->searchable(),
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
            'index' => ManageFormFieldOptions::route('/'),
        ];
    }
}
