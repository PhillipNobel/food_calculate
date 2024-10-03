<?php

namespace App\Filament\Resources\CategoryStockResource\Pages;

use App\Filament\Resources\CategoryStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoryStocks extends ListRecords
{
    protected static string $resource = CategoryStockResource::class;

    protected static ?string $title = 'Stock Categories';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
