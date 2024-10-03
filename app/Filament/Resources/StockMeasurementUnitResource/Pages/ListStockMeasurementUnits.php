<?php

namespace App\Filament\Resources\StockMeasurementUnitResource\Pages;

use App\Filament\Resources\StockMeasurementUnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockMeasurementUnits extends ListRecords
{
    protected static string $resource = StockMeasurementUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
