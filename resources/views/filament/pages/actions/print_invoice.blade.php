<div id="invoiceModal">
    <div class="border-b py-2">
        <h1 class="hidden-print font-bold text-2xl text-center text-black">
            {{ config('app.name') }}
        </h1>
        <div class="text-sm text-center">
            <p>{{ Filament\Facades\Filament::getTenant()->address }}</p>
            <p>{{ Filament\Facades\Filament::getTenant()->contact_phone }}</p>
        </div>
    </div>
    <div class="flex justify-between mb-6 text-gray-700 py-2 border-b text-sm">
        <div>
            <h2 class="font-bold">Customer:</h2>
            <p class="text-gray-700">
                {{ $record->customer->name }}
            </p>
        </div>
        <div class="text-sm">
            <p>#{{ $record->ref }}</p>
            <p>{{ $record->created_at->format('jS M, Y h:mA') }}</p>
        </div>
    </div>
    <table class="w-full mb-8 py-3 text-sm">
        <thead>
            <tr>
                <th class="text-left font-bold text-gray-700 pt-2">Description</th>
                <th class="text-left font-bold text-gray-700 pt-2">QtyxPrice</th>
                <th class="text-right font-bold text-gray-700 pt-2">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($record->purchaseItems as $item)
                <tr>
                    <td class="text-left text-gray-700">
                        {{ str($item->product->name)->excerpt(null, ['radius' => 20]) }}
                    </td>
                    <td class="text-left text-gray-700">
                        {{ $item->quantity }}x₦{{ number_format($item->unit_price, 2) }}
                    </td>
                    <td class="text-right text-gray-700">
                        ₦{{ number_format($item->total_price, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if ($record->discount > 0)
                <tr>
                    <td class="text-left font-bold text-gray-700" style="padding-top: 10px">
                        Discount
                    </td>
                    <td></td>
                    <td class="text-right font-bold text-gray-700">
                        ₦{{ number_format($record->discount, 2) }}
                    </td>
                </tr>
            @endif
            @if ($record->vat > 0)
                <tr>
                    <td class="text-left font-bold text-gray-700">VAT</td>
                    <td></td>
                    <td class="text-right font-bold text-gray-700">
                        ₦{{ number_format($record->vat, 2) }}
                    </td>
                </tr>
            @endif
            <tr>
                <td class="text-left font-bold text-gray-700">Total</td>
                <td></td>
                <td class="text-right font-bold text-gray-700">
                    ₦{{ number_format($record->items_total, 2) }}
                </td>
            </tr>
            <tr>
                <td class="text-left font-bold text-gray-700">Grand Total</td>
                <td></td>
                <td class="text-right font-bold text-gray-700">
                    ₦{{ number_format($record->grand_total, 2) }}
                </td>
            </tr>
            <tr>
                <td class="text-left text-gray-700" style="padding-top: 10px">
                    Mode of Payment
                </td>
                <td></td>
                <td class="text-right text-gray-700 whitespace-nowrap">
                    {{ $record->paymentType->name }}
                </td>
            </tr>
        </tfoot>
    </table>
    <div class="py-2 border-t mt-2 text-center">
        <p class="text-gray-700 text-sm mb-1">Thanks for you patronage!</p>
        <p class="text-gray-700 text-xs">
            Order bought in good condition can not be returned!
        </p>
    </div>
</div>
