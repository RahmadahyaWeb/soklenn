<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Invoice extends Page
{
    use InteractsWithRecord;

    protected static string $resource = InvoiceResource::class;

    protected string $view = 'filament.resources.invoices.pages.invoice';

    public function mount(int|string $record): void
    {
        $this->record = static::getModel()::with([
            'order.orderDetails.variant',
            'order.customer'
        ])
            ->findOrFail($record);
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Print')
                ->icon(Heroicon::Printer)
                ->requiresConfirmation()
        ];
    }
}
