<?php

namespace App\Filament\Resources\Forms\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FormForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('audience')
                    ->required()
                    ->default('company'),
                Toggle::make('is_published')
                    ->required(),
                Toggle::make('is_locked')
                    ->required(),
                DateTimePicker::make('published_at'),
                TextInput::make('meta'),
            ]);
    }
}
