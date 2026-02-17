<x-filament-panels::page>

    <div class="w-full max-w-6xl mx-auto bg-white text-gray-800 p-4 sm:p-6 md:p-10 sm:rounded-lg shadow">

        {{-- Header --}}
        <div class="flex flex-col gap-6 sm:flex-row sm:justify-between border-b pb-6">

            {{-- Left --}}
            <div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    INVOICE
                </h1>

                <div class="mt-3 space-y-1 text-xs sm:text-sm text-gray-500">

                    <div class="grid grid-cols-[130px_10px_1fr]">
                        <div>Invoice Number</div>
                        <div>:</div>
                        <div>{{ $record->invoice }}</div>
                    </div>

                    <div class="grid grid-cols-[130px_10px_1fr]">
                        <div>Invoice Date</div>
                        <div>:</div>
                        <div>{{ date('d-m-Y h:i:s', strtotime($record->created_at)) }}</div>
                    </div>

                </div>

            </div>


            {{-- Right --}}
            <div class="text-left sm:text-right">

                <h2 class="text-lg sm:text-xl font-semibold">
                    Soklenn
                </h2>

                <p class="text-xs sm:text-sm text-gray-500">
                    Komplek Sejahtera Mandiri Asri, Blok D No. 115
                </p>

                <p class="text-xs sm:text-sm text-gray-500">
                    081247189174
                </p>

                <p class="text-xs sm:text-sm text-gray-500">
                    soklenn2025@gmail.com
                </p>

            </div>

        </div>



        {{-- Customer --}}
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6">

            <div>

                <p class="font-semibold text-sm text-gray-600 mb-2">
                    Bill To
                </p>

                <p class="font-medium">
                    {{ $record->order->customer->name }}
                </p>

                <p class="text-sm text-gray-500">
                    {{ $record->order->customer->address }}
                </p>

                <p class="text-sm text-gray-500">
                    {{ $record->order->customer->phone }}
                </p>

            </div>

        </div>



        {{-- Table --}}
        <div class="mt-8 overflow-x-auto">

            <table class="w-full min-w-[600px]">

                <thead>

                    <tr class="bg-gray-100 text-xs sm:text-sm uppercase">

                        <th class="p-3 text-left">Product</th>

                        <th class="p-3 text-center">Qty</th>

                        <th class="p-3 text-right">Price</th>

                        <th class="p-3 text-right">Total</th>

                    </tr>

                </thead>


                <tbody class="divide-y text-sm">

                    @foreach ($record->order->orderDetails as $item)
                        <tr>

                            <td class="p-3">
                                {{ $item->variant->code }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $item->qty }}
                            </td>

                            <td class="p-3 text-right">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </td>

                            <td class="p-3 text-right">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

        {{-- Total --}}
        <div class="mt-8 flex justify-start sm:justify-end">

            <div class="w-full sm:max-w-sm space-y-2 text-sm">

                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($record->order->total_price, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Disc</span>
                    <span>Rp {{ number_format($item->order->discount_amount, 0, ',', '.') }}</span>
                </div>

                <div class="border-t pt-2 flex justify-between font-bold text-base sm:text-lg">

                    <span>Total</span>

                    <span class="text-primary-600">
                        Rp {{ number_format($item->order->total_payment, 0, ',', '.') }}
                    </span>

                </div>

            </div>

        </div>



        {{-- Notes --}}
        <div class="mt-10 border-t pt-6">

            <p class="text-sm font-semibold">
                Notes
            </p>

            <p class="text-sm text-gray-500">
                Terima kasih atas pembelian anda.
            </p>

        </div>
    </div>

</x-filament-panels::page>
