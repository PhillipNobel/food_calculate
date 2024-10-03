<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryStockResource\Pages;
use App\Filament\Resources\CategoryStockResource\RelationManagers;
use App\Models\CategoryStock;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Collection;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryStockResource extends Resource
{
    protected static ?string $model = CategoryStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Inventory Management';

    protected static ?string $navigationLabel = 'Categories';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Category Name'),
                
                Select::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parent', 'name')
                    ->nullable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Category Name'),

                TextColumn::make('parent.name')
                    ->label('Parent Category')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (CategoryStock $record) {
                        if ($record->stocks()->exists()) {
                            // Retornamos uma mensagem de erro
                            return 'This category has associated stocks and cannot be deleted.';
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function (Collection $records, $livewire) {
                        foreach ($records as $record) {
                            if ($record->stocks()->exists()) {
                                // Adicionamos uma mensagem de erro ao componente Livewire
                                $livewire->addError('bulkDelete', 'One or more categories have associated stocks and cannot be deleted.');
                                return false;
                            }
                        }
                    }),
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
            'index' => Pages\ListCategoryStocks::route('/'),
            'create' => Pages\CreateCategoryStock::route('/create'),
            'edit' => Pages\EditCategoryStock::route('/{record}/edit'),
        ];
    }
}
