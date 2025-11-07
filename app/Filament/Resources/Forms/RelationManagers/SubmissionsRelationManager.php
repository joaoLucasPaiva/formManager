<?php

namespace App\Filament\Resources\Forms\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $title = 'Respostas Recebidas';
    protected static string|\BackedEnum|null $icon = 'heroicon-o-inbox-stack';
    protected static ?int $navigationSort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
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
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('answers_count')
                    ->label('Campos')
                    ->counts('answers')
                    ->badge()
                    ->color('info')
                    ->suffix(' campos'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recebido em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->created_at->format('d/m/Y H:i:s')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('De'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Até'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                Tables\Actions\Action::make('view')
                    ->label('Ver Detalhes')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record): string => route('filament.admin.resources.form-submissions.view', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('view_all')
                    ->label('Ver Todas as Respostas')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn () => route('filament.admin.resources.form-submissions.index'))
                    ->color('gray'),
            ])
            ->emptyStateHeading('Nenhuma resposta ainda')
            ->emptyStateDescription('Quando alguém preencher este formulário, as respostas aparecerão aqui.')
            ->emptyStateIcon('heroicon-o-inbox');
    }
}
