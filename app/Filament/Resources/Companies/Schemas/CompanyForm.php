<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('category')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Address')
                    ->schema([
                        TextInput::make('street')
                            ->maxLength(255),
                        TextInput::make('postal_code')
                            ->maxLength(255),
                        TextInput::make('city')
                            ->maxLength(255),
                    ])
                    ->columns(3),
                Section::make('Scraping Data')
                    ->schema([
                        TextInput::make('source_url')
                            ->url()
                            ->maxLength(255),
                        DateTimePicker::make('scraped_at'),
                    ])
                    ->columns(2),
            ]);
    }
}
