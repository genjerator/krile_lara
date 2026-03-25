<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Template Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('A descriptive name for this email template'),
                        Checkbox::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active templates can be used to send emails'),
                    ])
                    ->columns(2),
                Section::make('Email Content')
                    ->schema([
                        TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Email subject line. You can use placeholders like {{company_name}}'),
                        Textarea::make('body_html')
                            ->required()
                            ->rows(15)
                            ->helperText('HTML content of the email. Available placeholders: {{company_name}}, {{category}}, {{city}}, {{street}}, {{postal_code}}, {{phone}}, {{website}}, {{email}}'),
                        Textarea::make('body_text')
                            ->rows(10)
                            ->helperText('Plain text version (optional)'),
                    ]),
            ]);
    }
}
