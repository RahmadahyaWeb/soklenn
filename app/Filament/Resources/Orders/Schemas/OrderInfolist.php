<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer.code')
                    ->label('Customer Code'),

                TextEntry::make('customer.name')
                    ->label('Customer Name'),

                TextEntry::make('total_price')
                    ->money('IDR'),

                TextEntry::make('date')
                    ->date(),

                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);

    }
}
