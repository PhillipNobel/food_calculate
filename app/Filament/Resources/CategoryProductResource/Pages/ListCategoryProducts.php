<?php

namespace App\Filament\Resources\CategoryProductResource\Pages;

use App\Filament\Resources\CategoryProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoryProducts extends ListRecords
{
    protected static string $resource = CategoryProductResource::class;

    protected static ?string $title = 'Product Categories';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
