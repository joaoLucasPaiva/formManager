<?php

namespace App\Filament\Resources\FormFieldOptions\Pages;

use App\Filament\Resources\FormFieldOptions\FormFieldOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageFormFieldOptions extends ManageRecords
{
    protected static string $resource = FormFieldOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
