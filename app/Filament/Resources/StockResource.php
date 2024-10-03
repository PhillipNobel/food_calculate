<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Laravel\Prompts\select;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Inventory Management';

    protected static ?string $navigationLabel = 'Inventory';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Item Name'),
                TextInput::make('quantity')
                    ->required()    
                    ->numeric()
                    ->label('Quantity'),
                TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->label('Unit Price'),
                Select::make('category_stock_id')
                    ->relationship('categoryStock', 'name')
                    ->required()
                    ->label('Stock Category'),
                Select::make('measurement_unit_id')
                    ->relationship('measurementUnit', 'symbol')
                    ->required()
                    ->label('Stock Unit')                
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextInputColumn::make('name')
                    ->label('Item Name')
                    ->rules(['required','max:255']),
                TextInputColumn::make('quantity')
                    ->label('Quantity')
                    ->rules(['required'], ['numeric']),
                TextInputColumn::make('unit_price')
                    ->label('Unit Price (NOK)')
                    //->money('NOK')
                    ->rules(['required','numeric']),
                TextColumn::make('measurementUnit.symbol')
                    ->label('Stock Unit'),
                TextColumn::make('categoryStock.name')
                    ->label('Stock Category'),
                TextColumn::make('total')
                    ->label('Total')
                    ->getStateUsing(function (Model $record): float {
                        return ($record->quantity * $record->unit_price);
                    })
                    ->money('NOK'),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->multiple()
                    ->label('Filter Categories')
                    ->relationship('categoryStock', 'name')
                    ->multiple()
                    ->placeholder('Select categories')
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            
            ->actions([
                Tables\Actions\EditAction::make(),
                

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
