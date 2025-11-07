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
                ->label('Título do Formulário')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set, $context) {
                    // Só gera slug automaticamente na criação ou se o slug estiver vazio
                    if ($context === 'create' || empty($context)) {
                        $set('slug', str($state)->slug()->toString());
                    }
                })
                ->helperText('Nome do formulário que será exibido'),

            Components\Hidden::make('slug')
                ->default(fn ($record) => $record?->slug ?? ''),

            Components\Textarea::make('description')
                ->label('Descrição')
                ->rows(3)
                ->helperText('Descreva o objetivo deste formulário (opcional)'),

            Components\Select::make('audience')
                ->label('Público-alvo')
                ->options([
                    'company'   => 'Empresa',
                    'inspector' => 'Vistoriador',
                ])
                ->required()
                ->default('company')
                ->helperText('Quem irá preencher este formulário?'),

            Components\KeyValue::make('meta')
                ->label('Metadados (Opcional)')
                ->addActionLabel('Adicionar Metadado')
                ->reorderable()
                ->helperText('Informações adicionais em formato chave-valor'),
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
                    ->sortable()
                    ->description(fn (FormModel $record) => $record->is_locked ? '🔒 Publicado e bloqueado' : '✏️ Rascunho'),

                Tables\Columns\TextColumn::make('slug')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // BadgeColumn pode estar deprecated nos stubs; usa badge() na TextColumn
                Tables\Columns\TextColumn::make('audience')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'company' => 'Empresa',
                        'inspector' => 'Vistoriador',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (FormModel $record): string =>
                        $record->is_locked ? 'Publicado' : 'Rascunho'
                    )
                    ->color(fn (FormModel $record): string =>
                        $record->is_locked ? 'success' : 'warning'
                    ),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicado em')
                    ->dateTime()
                    ->since()
                    ->placeholder('Não publicado')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn (FormModel $record) => $record->is_locked),

                Action::make('view')
                    ->label('Ver Formulário')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->visible(fn (FormModel $record) => $record->is_published)
                    ->url(fn (FormModel $record): string => route('form.show', $record->slug))
                    ->openUrlInNewTab(),

                Action::make('copy_link')
                    ->label('Copiar Link')
                    ->icon('heroicon-o-clipboard')
                    ->color('info')
                    ->visible(fn (FormModel $record) => $record->is_published)
                    ->requiresConfirmation()
                    ->modalHeading('Link do Formulário')
                    ->modalDescription('Copie o link abaixo para compartilhar o formulário:')
                    ->modalContent(fn (FormModel $record) => view('filament.modals.copy-link', [
                        'url' => route('form.show', $record->slug)
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar'),

                Action::make('publish')
                    ->label('Publicar')
                    ->icon('heroicon-o-rocket-launch')
                    ->color('success')
                    ->visible(fn (FormModel $record) => ! $record->is_published)
                    ->requiresConfirmation()
                    ->modalHeading('Publicar Formulário')
                    ->modalDescription('Após publicar, o formulário será bloqueado e não poderá mais ser editado. Tem certeza?')
                    ->modalSubmitActionLabel('Sim, publicar')
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
            RelationManagers\SectionsRelationManager::class,
            RelationManagers\FieldsRelationManager::class,
            RelationManagers\SubmissionsRelationManager::class,
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
