<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product_variant_id')
                    ->required()
                    ->numeric(),
                Select::make('type')
                    ->options(['in' => 'In', 'out' => 'Out'])
                    ->required(),
                TextInput::make('qty')
                    ->required()
                    ->numeric(),
                TextInput::make('stock_before')
                    ->required()
                    ->numeric(),
                TextInput::make('stock_after')
                    ->required()
                    ->numeric(),
                TextInput::make('reference'),
                TextInput::make('note'),
            ]);
    }
}
