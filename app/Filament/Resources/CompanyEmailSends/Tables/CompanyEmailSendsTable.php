<?php

namespace App\Filament\Resources\CompanyEmailSends\Tables;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompanyEmailSendsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('emailTemplate.name')
                    ->label('Template')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('attempts')
                    ->sortable(),
                TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('error_message')
                    ->label('Error')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->error_message)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                    ]),
                SelectFilter::make('emailTemplate')
                    ->relationship('emailTemplate', 'name')
                    ->label('Template'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->infolist(function (Infolist $infolist): Infolist {
                        return $infolist
                            ->schema([
                                Section::make('Company Details')
                                    ->schema([
                                        TextEntry::make('company.name')
                                            ->label('Company Name'),
                                        TextEntry::make('company.email')
                                            ->label('Email Address')
                                            ->copyable(),
                                        TextEntry::make('company.city')
                                            ->label('City'),
                                        TextEntry::make('company.category')
                                            ->label('Category'),
                                    ])
                                    ->columns(2),
                                Section::make('Email Details')
                                    ->schema([
                                        TextEntry::make('emailTemplate.name')
                                            ->label('Template Used'),
                                        TextEntry::make('emailTemplate.subject')
                                            ->label('Subject Line'),
                                        TextEntry::make('status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'sent' => 'success',
                                                'failed' => 'danger',
                                                'pending' => 'warning',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('attempts')
                                            ->label('Send Attempts'),
                                        TextEntry::make('sent_at')
                                            ->label('Sent At')
                                            ->dateTime(),
                                        TextEntry::make('created_at')
                                            ->label('Created At')
                                            ->dateTime(),
                                    ])
                                    ->columns(2),
                                Section::make('Error Information')
                                    ->schema([
                                        TextEntry::make('error_message')
                                            ->label('Error Message')
                                            ->placeholder('No errors')
                                            ->columnSpanFull(),
                                    ])
                                    ->hidden(fn ($record) => empty($record->error_message)),
                            ]);
                    }),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
