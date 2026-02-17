<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Exports\OrderExporter;
use App\Models\Invoice;
use App\Services\InventoryServices;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Order Code')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('customer.code')
                    ->label('Customer Code')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('customer.name')
                    ->label('Customer Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('total_price')
                    ->money('IDR'),
                TextColumn::make('discount')
                    ->suffix('%')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('discount_amount')
                    ->money('IDR')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_payment')
                    ->label('Total Payment')
                    ->money('IDR'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'confirmed' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('generate_invoice')
                        ->label('Generate Invoice')
                        ->icon('heroicon-o-document-text')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status !== 'paid')
                        ->action(function ($record) {

                            try {

                                DB::transaction(function () use ($record, &$invoice_number) {

                                    // generate nomor invoice
                                    $invoice_number = 'INV-'.date('Ymd').'-'.str_pad(
                                        Invoice::count() + 1,
                                        4,
                                        '0',
                                        STR_PAD_LEFT
                                    );

                                    // insert invoice
                                    Invoice::create([
                                        'invoice' => $invoice_number,
                                        'order_id' => $record->id,
                                        'note' => 'Terima kasih atas pembelian anda.'
                                    ]);

                                    // update order status
                                    $record->update([
                                        'status' => 'paid',
                                    ]);

                                    // reduce stock
                                    InventoryServices::reduce_stock_from_order(
                                        $record,
                                        $invoice_number,
                                        'Penjualan Invoice'
                                    );

                                });

                                // success notification
                                Notification::make()
                                    ->title('Invoice generated successfully')
                                    ->success()
                                    ->send();

                            } catch (\Throwable $e) {

                                // error notification
                                Notification::make()
                                    ->title('Error')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();

                            }

                        }),

                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(OrderExporter::class)
                    ->label('Download Excel')
                    ->icon(Heroicon::DocumentArrowDown)
                    ->color('primary'),
            ]);
    }
}
