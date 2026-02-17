<?php

namespace App\Filament\Resources\Orders\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Draft', Order::where('status', 'draft')->count())
                ->description('Orders created but not yet confirmed')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray'),

            Stat::make('Confirmed', Order::where('status', 'confirmed')->count())
                ->description('Orders confirmed and awaiting payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Revenue', 'IDR '.number_format(Order::where('status', 'paid')->sum('total_payment'), 0))
                ->description('Total revenue from successfully paid orders')
                ->descriptionIcon('heroicon-m-check')
                ->color('success'),
        ];
    }
}
