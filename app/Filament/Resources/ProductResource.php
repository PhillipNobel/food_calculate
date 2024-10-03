<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Stock;
use Barryvdh\Debugbar\Facades\Debugbar;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Container\Attributes\DB;
use Illuminate\Container\Attributes\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB as FacadesDB;

use function Laravel\Prompts\select;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Products Management';

    protected static ?string $navigationLabel = 'Products';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Product Name'),

                Textarea::make('description')
                    ->label('Product Description'),

                Select::make('category_product_id')
                    ->relationship('categoryProducts', 'name')
                    ->required()
                    ->label('Product Category'),

                Repeater::make('productStocks')
                    ->relationship() // Relacionamento correto com a tabela pivot
                    ->schema([
                        Select::make('stock_id')
                            ->options(Stock::all()->pluck('name', 'id'))
                            ->searchable()
                            ->label('Ingredient')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                // Quando o ingrediente for selecionado, obtenha o preço unitário
                                $stock = Stock::find($state);
                                if ($stock) {
                                    $set('unit_price', $stock->unit_price); // Define o preço unitário                                    
                                }

                                //atualize o preço total do ingrediente
                                $unitPrice = $get('unit_price');
                                $quantity = $get('quantity');
                                if ($unitPrice && $quantity) {
                                    $set('ingredient_price', $unitPrice * $quantity); // Define o preço total
                                 
                                }
                                // Recalcula o custo total do produto
                                self::updateTotalCost($set, $get);
                            }),
                
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->required()
                            ->live()
                            ->reactive() // Atualiza dinamicamente quando a quantidade muda
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                // Após inserir a quantidade, atualize o preço total do ingrediente
                                $unitPrice = $get('unit_price');
                                $quantity = $get('quantity');
                                if ($unitPrice && $quantity) {
                                    $set('ingredient_price', $unitPrice * $quantity); // Define o preço total
                                 
                                }
                                // Recalcula o custo total do produto
                                self::updateTotalCost($set, $get);
                            }),
                
                        TextInput::make('unit_price')
                            ->label('Unit Price (NOK)')
                            ->readOnly() // Não permite edição
                            ->numeric(),
                
                        TextInput::make('ingredient_price')
                            ->label('Ingredient Price (NOK)')
                            ->readOnly() // Não permite edição
                            ->numeric(),
                    ])
                    ->columns(4)  // Define o número de colunas do repeater para layout
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        // Recalcula o custo total do produto
                        self::updateTotalCost($set, $get);
                    }),
                     

                TextInput::make('total_cost')
                    ->label('Total Product Cost (NOK)')
                    ->readOnly() // Não permite edição
                    ->reactive()
                    ->numeric(),
                ])
                    
            ->columns(1);
                    
    }
    
    protected static function updateTotalCost(callable $set, callable $get): void
{
    $productStocks = $get('productStocks') ?? [];
    $totalCost = 0;

    foreach ($productStocks as $stock) {
        $quantity = $stock['quantity'] ?? 0;
        $unitPrice = $stock['unit_price'] ?? 0;
        // Cálculo do preço do ingrediente
        $ingredientPrice = $quantity * $unitPrice;
        $totalCost += $ingredientPrice;
    }

    // Atualizando o campo total_cost
    $set('total_cost', $totalCost);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Product Name'),
                TextColumn::make('description')
                    ->label('Description'),
                TextColumn::make('categoryProducts.name')
                    ->label('Product Category'),
            ])
            ->filters([
                SelectFilter::make('category_product_id')
                    ->multiple()
                    ->label('Filter Categories')
                    ->relationship('categoryProducts', 'name')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function saving(Product $record, array $data): void
    {
    // Verifique se os dados do repeater estão definidos
    $stocksData = $data['productStocks'] ?? [];

    $totalCost = 0; // Inicie o custo total como 0

    // Calcule o custo total somando os preços dos ingredientes
    foreach ($stocksData as $stock) {
        if (isset($stock['ingredient_price'])) {
            $totalCost += $stock['ingredient_price'];
        }
    }

    // Defina o total_cost no record antes de salvar
    $record->total_cost = $totalCost;

    // Prossiga com a sincronização dos dados do repeater
    $syncData = [];

    foreach ($stocksData as $stock) {
        if (isset($stock['stock_id'], $stock['quantity'], $stock['unit_price'], $stock['ingredient_price'])) {
            $syncData[$stock['stock_id']] = [
                'quantity' => $stock['quantity'],
                'unit_price' => $stock['unit_price'],
                'ingredient_price' => $stock['ingredient_price'],
            ];
        }
    }

    $record->stocks()->sync($syncData);
    }

    
    
    /*
    public static function saving(Product $record, array $data): void
    {
    $stocksData = $data['productStocks'] ?? [];
    $syncData = [];
    $totalCost = 0; // Inicializa o custo total

    foreach ($stocksData as $stock) {
        if (isset($stock['ingredient_price'], $stock['quantity'])) {
            // Calcule o custo total com base no preço do ingrediente e na quantidade
            $totalCost += $stock['ingredient_price']; // Considerando que ingredient_price já é o total por item
            $syncData[$stock['stock_id']] = [
                'quantity' => $stock['quantity'],
                'unit_price' => $stock['unit_price'], // Se necessário
                'ingredient_price' => $stock['ingredient_price'],
            ];
        }
    }

    // Salva o custo total no modelo Product
    $record->total_cost = $totalCost;

    // Sincroniza os ingredientes com a tabela pivot
    $record->stocks()->sync($syncData);
    }*/



    /*public static function saving(Product $record, array $data): void
    {
    $stocksData = $data['productStocks'] ?? [];
    $syncData = [];
    $totalCost = 0;

    foreach ($stocksData as $stock) {
        if (isset($stock['stock_id'], $stock['quantity'], $stock['unit_price'], $stock['ingredient_price'])) {
            $syncData[$stock['stock_id']] = [
                'stock_id' => $stock['stock_id'],
                'quantity' => $stock['quantity'],
                'unit_price' => $stock['unit_price'],
                'ingredient_price' => $stock['ingredient_price'],
            ];

            // Soma os valores dos ingredient_price para calcular o custo total
            $totalCost += $stock['ingredient_price'];
        }
    }

    // Salva os ingredientes na tabela pivot
    $record->stocks()->sync($syncData);

    // Atualiza o campo total_cost no produto
    $record->total_cost = $totalCost;
    }*/

    /*public static function saved(Product $record): void
    {
    // Calcular o custo total após salvar o produto
    $totalCost = $record->stocks()->sum('ingredient_price');

    // Atualizar o campo 'total_cost'
    $record->update(['total_cost' => $totalCost]);
    }*/

    /*public static function saving(Product $record, array $data): void
    {
    $stocksData = $data['productStocks'] ?? [];

    $syncData = [];

    foreach ($stocksData as $stock) {
        if (isset($stock['stock_id'], $stock['quantity'], $stock['unit_price'], $stock['ingredient_price'])) {
            $syncData[$stock['stock_id']] = [
                'stock_id' => $stock['stock_id'], // Adicione esta linha
                'quantity' => $stock['quantity'],
                'unit_price' => $stock['unit_price'],
                'ingredient_price' => $stock['ingredient_price'],
            ];
        }
    }

    $record->stocks()->sync($syncData);
    }*/

}
