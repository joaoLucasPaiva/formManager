<?php

namespace App\Filament\Resources\Forms\Pages;

use App\Filament\Resources\Forms\FormResource;
use App\Models\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateForm extends CreateRecord
{
    protected static string $resource = FormResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        // Garante que o slug seja gerado caso não exista
        if (empty($data['slug']) && !empty($data['title'])) {
            $baseSlug = str($data['title'])->slug()->toString();
            $slug = $baseSlug;
            $counter = 1;

            // Garante que o slug seja único
            while (Form::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $data['slug'] = $slug;
        }

        return $data;
    }
}
