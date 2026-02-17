<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class OrderForm
{
    protected static function recalculate(Get $get, Set $set): void
    {
        $items = $get('orderDetails') ?? [];

        $total = collect($items)->sum(function ($item) {
            $price = $item['price'] ?? 0;
            $qty = $item['qty'] ?? 0;

            return $price * $qty;
        });

        $set('total_price', $total);

        $discount = $get('discount') ?? 0;
        $discountAmount = ($total * $discount) / 100;

        $set('discount_amount', $discountAmount);
        $set('total_payment', $total - $discountAmount);
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->default(now())
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                TextInput::make('code')
                    ->disabled()
                    ->dehydrated(false)
                    ->default(function () {
                        $last = Order::orderByDesc('id')->first();
                        $number = $last ? ((int) substr($last->code, -4)) + 1 : 1;

                        return 'PO-'.str_pad($number, 4, '0', STR_PAD_LEFT);
                    }),

                Grid::make(2)
                    ->schema([
                        Section::make('Customer Information')
                            ->schema([
                                Select::make('customer_id')
                                    ->relationship('customer', 'name')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->code.' - '.$record->name
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $customer = Customer::find($state);

                                        $set('customer_phone', $customer?->phone);
                                        $set('customer_address', $customer?->address);
                                    })
                                    ->afterStateHydrated(function ($state, Set $set) {
                                        if (! $state) {
                                            return;
                                        }

                                        $customer = Customer::find($state);

                                        $set('customer_phone', $customer?->phone);
                                        $set('customer_address', $customer?->address);
                                    })
                                    ->required(),

                                TextInput::make('customer_phone')
                                    ->prefix('+62')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('customer_address')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),

                        Section::make('Order Details')
                            ->schema([
                                Repeater::make('orderDetails')
                                    ->relationship()
                                    ->schema([
                                        Select::make('product_variant_id')
                                            ->relationship('variant', 'code')
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $product = ProductVariant::find($state);
                                                $price = $product->price ?? 0;
                                                $qty = $get('qty') ?? 1;

                                                $set('price', $price);
                                                $set('subtotal', $price * $qty);

                                                $items = $get('../../orderDetails') ?? [];

                                                $total = collect($items)->sum(function ($item) {
                                                    return ($item['price'] ?? 0) * ($item['qty'] ?? 0);
                                                });

                                                $set('../../total_price', $total);

                                                $discount = $get('../../discount') ?? 0;
                                                $discount_amount = ($total * $discount) / 100;
                                                $grand_total = $total - $discount_amount;

                                                $set('../../discount_amount', $discount_amount);
                                                $set('../../total_payment', $grand_total);
                                            }),

                                        TextInput::make('price')
                                            ->prefix('IDR')
                                            ->disabled()
                                            ->dehydrated(),

                                        TextInput::make('qty')
                                            ->numeric()
                                            ->default(1)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $price = $get('price') ?? 0;
                                                $set('subtotal', $price * $state);

                                                $items = $get('../../orderDetails') ?? [];

                                                $total = collect($items)->sum(function ($item) {
                                                    return ($item['price'] ?? 0) * ($item['qty'] ?? 0);
                                                });

                                                $set('../../total_price', $total);

                                                $discount = $get('../../discount') ?? 0;
                                                $discount_amount = ($total * $discount) / 100;
                                                $grand_total = $total - $discount_amount;

                                                $set('../../discount_amount', $discount_amount);
                                                $set('../../total_payment', $grand_total);
                                            }),

                                        TextInput::make('subtotal')
                                            ->prefix('IDR')
                                            ->disabled()
                                            ->dehydrated(),
                                    ])
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::recalculate($get, $set);
                                    })
                                    ->live(),
                            ])
                            ->columnSpanFull(),
                    ]),

                Grid::make()
                    ->schema([
                        Section::make('Payment Detail')
                            ->schema([
                                TextInput::make('total_price')
                                    ->label('Subtotal')
                                    ->prefix('IDR')
                                    ->disabled()
                                    ->dehydrated()
                                    ->numeric()
                                    ->required(),

                                TextInput::make('discount')
                                    ->label('Discount (%)')
                                    ->numeric()
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                        self::recalculate($get, $set);
                                    }),

                                TextInput::make('discount_amount')
                                    ->label('Discount Amount')
                                    ->prefix('IDR')
                                    ->disabled()
                                    ->dehydrated()
                                    ->numeric(),

                                TextInput::make('total_payment')
                                    ->label('Grand Total')
                                    ->prefix('IDR')
                                    ->disabled()
                                    ->dehydrated()
                                    ->numeric()
                                    ->required(),

                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'confirmed' => 'Confirmed',
                                        'paid' => 'Paid',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('draft')
                                    ->required(),

                            ])->columnSpanFull(),
                    ]),
            ]);
    }
}
