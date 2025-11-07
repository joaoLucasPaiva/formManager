<?php

namespace App\Filament\Resources\FormSubmissions;

use App\Filament\Resources\FormSubmissions\Pages;
use App\Models\Form;
use App\Models\FormSubmission;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox-stack';
    protected static string|\UnitEnum|null $navigationGroup = 'Form Builder';
    protected static ?string $navigationLabel = 'Respostas';
    protected static ?string $modelLabel = 'Resposta';
    protected static ?string $pluralModelLabel = 'Respostas';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            // Não teremos form de criação/edição, apenas visualização
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                Tables\Columns\TextColumn::make('identifier_value')
                    ->label('Identificação')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Não identificado')
                    ->icon('heroicon-o-user')
                    ->copyable()
                    ->weight('bold')
                    ->description(fn (FormSubmission $record) => 'IP: ' . ($record->ip ?? 'Não registrado')),

                Tables\Columns\TextColumn::make('form.title')
                    ->label('Formulário')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('answers_count')
                    ->label('Campos')
                    ->counts('answers')
                    ->badge()
                    ->color('info')
                    ->suffix(' campos'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->description(fn (FormSubmission $record) => $record->created_at->format('d/m/Y H:i:s')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('form_id')
                    ->label('Formulário')
                    ->options(Form::pluck('title', 'id'))
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Ver Respostas')
                    ->icon('heroicon-o-eye')
                    ->url(fn (FormSubmission $record): string => static::getUrl('view', ['record' => $record]))
                    ->color('gray'),

                DeleteAction::make(),
            ])
            ->emptyStateHeading('Nenhuma resposta ainda')
            ->emptyStateDescription('Quando alguém preencher um formulário, as respostas aparecerão aqui.')
            ->emptyStateIcon('heroicon-o-inbox');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormSubmissions::route('/'),
            'view' => Pages\ViewFormSubmission::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'success' : null;
    }
}
