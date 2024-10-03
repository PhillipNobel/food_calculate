<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMeasurementUnitResource\Pages;
use App\Filament\Resources\StockMeasurementUnitResource\RelationManagers;
use App\Models\StockMeasurementUnit;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockMeasurementUnitResource extends Resource
{
    protected static ?string $model = StockMeasurementUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Inventory Management';

    protected static ?string $navigationLabel = 'Measurament Units';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Unit Name'),
                TextInput::make('symbol') // Adicione esta linha para o símbolo
                    ->required() // Defina se é obrigatório ou não
                    ->label('Symbol'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Unit Name'),
                TextColumn::make('symbol')
                    ->label('Unit Symbol'),
            ])
            ->filters([
                //
            ])
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
            'index' => Pages\ListStockMeasurementUnits::route('/'),
            'create' => Pages\CreateStockMeasurementUnit::route('/create'),
            'edit' => Pages\EditStockMeasurementUnit::route('/{record}/edit'),
        ];
    }
}
