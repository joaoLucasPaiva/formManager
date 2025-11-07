<?php

namespace App\Filament\Resources\FormFields\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Rótulo da Opção')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Texto exibido ao respondente'),

                TextInput::make('value')
                    ->label('Valor')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Valor enviado no formulário (sem espaços)')
                    ->alphaDash(),

                TextInput::make('position')
                    ->label('Posição')
                    ->numeric()
                    ->default(1)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')
                    ->label('Rótulo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('value')
                    ->label('Valor')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->size('sm'),

                TextColumn::make('position')
                    ->label('Ordem')
                    ->sortable(),
            ])
            ->defaultSort('position')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('position');
    }
}
