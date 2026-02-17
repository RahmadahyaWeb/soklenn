<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('invoice')
                    ->required(),
                TextInput::make('order_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
