<?php

namespace App\Filament\Resources\CategoryStockResource\Pages;

use App\Filament\Resources\CategoryStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoryStock extends EditRecord
{
    protected static string $resource = CategoryStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
