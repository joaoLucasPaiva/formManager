<?php

namespace App\Filament\Resources\FormSections;

use App\Filament\Resources\FormSections\Pages\ManageFormSections;
use App\Models\FormSection;
use App\Models\Form;
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

class FormSectionResource extends Resource
{
    protected static ?string $model = FormSection::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';
    protected static string|\UnitEnum|null $navigationGroup = 'Form Builder';
    protected static ?string $navigationLabel = 'Seções';
    protected static ?string $modelLabel = 'Seção';
    protected static ?string $pluralModelLabel = 'Seções';

    // Ocultar do menu - seções são criadas inline ao adicionar campos
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Select::make('form_id')
                    ->label('Formulário')
                    ->options(Form::pluck('title', 'id'))
                    ->required()
                    ->searchable(),

                Components\TextInput::make('title')
                    ->label('Título da Seção')
                    ->required()
                    ->maxLength(255),

                Components\Textarea::make('description')
                    ->label('Descrição')
                    ->rows(3)
                    ->maxLength(65535)
                    ->nullable(),

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
                Tables\Columns\TextColumn::make('form.title')
                    ->label('Formulário')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),

                Tables\Columns\TextColumn::make('position')
                    ->label('Ordem')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fields_count')
                    ->label('Campos')
                    ->counts('fields')
                    ->badge(),
            ])
            ->defaultSort('position')
            ->filters([
                Tables\Filters\SelectFilter::make('form_id')
                    ->label('Formulário')
                    ->options(Form::pluck('title', 'id'))
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
            'index' => ManageFormSections::route('/'),
        ];
    }
}
