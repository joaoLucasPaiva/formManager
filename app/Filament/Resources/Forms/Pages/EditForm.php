<?php

namespace App\Filament\Resources\Forms\Pages;

use App\Filament\Resources\Forms\FormResource;
use App\Models\Form;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditForm extends EditRecord
{
    protected static string $resource = FormResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        // Verifica se o formulário está bloqueado
        if ($this->record->is_locked) {
            Notification::make()
                ->warning()
                ->title('Formulário Bloqueado')
                ->body('Este formulário foi publicado e não pode mais ser editado.')
                ->persistent()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->disabled(fn (Form $record) => $record->is_locked)
                ->tooltip(fn (Form $record) => $record->is_locked ? 'Formulários publicados não podem ser excluídos' : null),
        ];
    }

    protected function beforeSave(): void
    {
        // Impede o salvamento se o formulário estiver bloqueado
        if ($this->record->is_locked) {
            Notification::make()
                ->danger()
                ->title('Ação Bloqueada')
                ->body('Formulários publicados não podem ser editados.')
                ->send();

            $this->halt();
        }
    }
}
