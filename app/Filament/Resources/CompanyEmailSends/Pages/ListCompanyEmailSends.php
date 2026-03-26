<?php

namespace App\Filament\Resources\CompanyEmailSends\Pages;

use App\Filament\Resources\CompanyEmailSends\CompanyEmailSendResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyEmailSends extends ListRecords
{
    protected static string $resource = CompanyEmailSendResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
