<?php

namespace App\Filament\Resources\CompanyEmailSends;

use App\Filament\Resources\CompanyEmailSends\Pages\ListCompanyEmailSends;
use App\Filament\Resources\CompanyEmailSends\Tables\CompanyEmailSendsTable;
use App\Models\CompanyEmailSend;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CompanyEmailSendResource extends Resource
{
    protected static ?string $model = CompanyEmailSend::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Email History';

    protected static ?string $pluralModelLabel = 'Email History';

    protected static ?string $modelLabel = 'Email Send';

    public static function table(Table $table): Table
    {
        return CompanyEmailSendsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanyEmailSends::route('/'),
        ];
    }
}
