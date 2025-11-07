<?php

namespace App\Filament\Resources\FormSections\Pages;

use App\Filament\Resources\FormSections\FormSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageFormSections extends ManageRecords
{
    protected static string $resource = FormSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
