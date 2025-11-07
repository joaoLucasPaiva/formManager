<?php

namespace App\Filament\Resources\Forms\RelationManagers;

use App\Models\FormSection;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Seções do Formulário';
    protected static string|\BackedEnum|null $icon = 'heroicon-o-folder';
    protected static ?int $navigationSort = 1;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\TextInput::make('title')
                    ->label('Título da Seção')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ex: Dados da Empresa')
                    ->helperText('Nome da seção que será exibido no formulário'),

                Components\Textarea::make('description')
                    ->label('Descrição (Opcional)')
                    ->rows(3)
                    ->placeholder('Descreva o que deve ser preenchido nesta seção')
                    ->helperText('Texto explicativo que aparecerá abaixo do título'),

                Components\Hidden::make('position')
                    ->default(fn (RelationManager $livewire, $record) =>
                        $record?->position ?? ($livewire->getOwnerRecord()->sections()->max('position') ?? 0) + 1
                    ),
            ]);
    }

    public function table(Table $table): Table
    {
        $isLocked = $this->getOwnerRecord()->is_locked;

        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('position')
                    ->label('#')
                    ->sortable()
                    ->width(60)
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título da Seção')
                    ->searchable()
                    ->icon('heroicon-o-bars-3')
                    ->iconColor('gray')
                    ->description(fn ($record) => $record->description),

                Tables\Columns\TextColumn::make('fields_count')
                    ->label('Campos')
                    ->counts('fields')
                    ->badge()
                    ->color('info')
                    ->suffix(' campos'),
            ])
            ->defaultSort('position', 'asc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->disabled(fn () => $isLocked)
                    ->tooltip(fn () => $isLocked ? 'Formulário publicado não pode ser editado' : null)
                    ->using(function (array $data, RelationManager $livewire): mixed {
                        $data['form_id'] = $livewire->getOwnerRecord()->id;

                        // Criar a seção
                        return $livewire->getOwnerRecord()->sections()->create($data);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->disabled(fn () => $isLocked)
                    ->tooltip(fn () => $isLocked ? 'Formulário publicado não pode ser editado' : null),

                DeleteAction::make()
                    ->disabled(fn () => $isLocked)
                    ->tooltip(fn () => $isLocked ? 'Formulário publicado não pode ser editado' : null)
                    ->modalHeading('Excluir Seção')
                    ->modalDescription('Os campos desta seção não serão excluídos, apenas ficarão sem seção.')
                    ->before(function ($record) {
                        // Move os campos para "sem seção"
                        $record->fields()->update(['form_section_id' => null]);
                    }),
            ])
            ->reorderable($isLocked ? null : 'position')
            ->emptyStateHeading('Nenhuma seção criada')
            ->emptyStateDescription('Seções ajudam a organizar campos relacionados. Crie seções para agrupar seus campos!')
            ->emptyStateIcon('heroicon-o-folder-plus')
            ->heading(fn () => $isLocked ? 'Seções do Formulário (somente leitura)' : 'Seções do Formulário - Arraste para reordenar ↕️');
    }
}
