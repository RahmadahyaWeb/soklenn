<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\Customer;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->disabled()
                    ->dehydrated(false)
                    ->default(function () {
                        $last = Customer::orderByDesc('id')->first();
                        $number = $last ? ((int) substr($last->code, -4)) + 1 : 1;

                        return 'CUST-'.str_pad($number, 4, '0', STR_PAD_LEFT);
                    }),

                TextInput::make('phone')
                    ->prefix('+62')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                Textarea::make('address')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
