<?php

namespace App\Filament\Resources\Forms;

use App\Filament\Resources\Forms\Pages;
use App\Filament\Resources\Forms\RelationManagers;
use App\Models\Form as FormModel;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;

// 💡 A tua versão do Filament pede Schema:
use Filament\Schemas\Schema;
use Filament\Forms\Components; // vamos continuar usando os Components do Forms

class FormResource extends Resource
{
    protected static ?string $model = FormModel::class;

    // tipos devem ser compatíveis com o pai: string|BackedEnum|null e string|UnitEnum|null
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string|\UnitEnum|null   $navigationGroup = 'Form Builder';

    // ✅ Assinatura compatível com o teu Resource base:
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Components\TextInput::make('title')
                ->required()
                ->maxLength(255),

            Components\TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true),

            Components\Textarea::make('description')
                ->rows(3),

            Components\Select::make('audience')
                ->options([
                    'company'   => 'Empresa',
                    'inspector' => 'Vistoriador',
                ])
                ->required()
                ->default('company'),

            Components\Toggle::make('is_published')->disabled(),
            Components\Toggle::make('is_locked')->disabled(),
            Components\DateTimePicker::make('published_at')->disabled(),

            // no v4 recente, prefira addActionLabel() (addButtonLabel é deprecated)
            Components\KeyValue::make('meta')
                ->addActionLabel('Adicionar')
                ->reorderable(),
        ])
        // trava a edição quando bloqueado
        ->disabled(fn (?FormModel $record) => $record?->is_locked === true);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->copyable(),

                // BadgeColumn pode estar deprecated nos stubs; usa badge() na TextColumn
                Tables\Columns\TextColumn::make('audience')
                    ->badge(),

                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\IconColumn::make('is_locked')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->since(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since(),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn (FormModel $record) => $record->is_locked),

                Action::make('publish')
                    ->label('Publicar')
                    ->icon('heroicon-o-rocket-launch')
                    ->color('success')
                    ->visible(fn (FormModel $record) => ! $record->is_published)
                    ->requiresConfirmation()
                    ->action(fn (FormModel $record) => $record->update([
                        'is_published' => true,
                        'is_locked'    => true,
                        'published_at' => now(),
                    ])),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FieldsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListForms::route('/'),
            'create' => Pages\CreateForm::route('/create'),
            'edit'   => Pages\EditForm::route('/{record}/edit'),
        ];
    }
}
