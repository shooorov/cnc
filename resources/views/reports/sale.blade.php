@extends('print')

@section('title', $heading)

@section('content')
    <div class="mb-8">
        <h1 class="mb-2 text-lg">Payment Method wise collection</h1>
        <table class="table-fixed w-full border-collapse">
            <thead>
                <tr>
                @foreach ($payment_methods as $item)
                    @if($item->id == $payment_method?->id)
                    <th class="w-16 border align-middle text-center py-1 px-2" scope="col">
                        {{ $item->name }}
                    </th>
                    @else
                    <td class="w-16 border align-middle text-center py-1 px-2" scope="col">
                        {{ $item->name }}
                    </td>
                    @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                @foreach ($payment_methods as $item)
                    @if($item->id == $payment_method?->id)
                    <th class="border align-middle text-center py-1 px-2">
                        {{ App\Helpers::toAmount($item->amount) }}
                    </th>
                    @else
                    <td class="border align-middle text-center py-1 px-2">
                        {{ App\Helpers::toAmount($item->amount) }}
                    </td>
                    @endif
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mb-8">
        <h1 class="mb-2 text-lg">Orders List</h1>
        <table class="table-fixed w-full mt-8 border-collapse">
            <thead>
                <tr>
                    <th class="w-16 border align-middle text-center py-1 px-2" scope="col">S.N.</th>
                    <th class="w-24 border align-middle text-center py-1 px-2" scope="col">Date</th>
                    <th class="w-24 border align-middle text-center py-1 px-2" scope="col">Time</th>
                    <th class="border align-middle text-center py-1 px-2" scope="col">Number</th>
                    <th class="border align-middle text-center py-1 px-2" scope="col">Customer</th>
                    <th class="border align-middle text-center py-1 px-2" scope="col">Items</th>
                    <th class="border align-middle text-center py-1 px-2" scope="col">Manager</th>
                    <th class="border align-middle text-center py-1 px-2" scope="col">Served By</th>
                    <th class="w-24 border align-middle text-center py-1 px-2" scope="col">Sub Total</th>
                    <th class="w-24 border align-middle text-center py-1 px-2" scope="col">Discount</th>
                    <th class="w-24 border align-middle text-center py-1 px-2" scope="col">VAT</th>
                    <th class="w-24 border align-middle text-center py-1 px-2" scope="col">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sum_sub_total = 0;
                    $sum_discount = 0;
                    $sum_vat_amount = 0;
                    $sum_total = 0;
                @endphp
                @foreach ($orders as $order)
                    @php
                        $sum_sub_total += $order->sub_total;
                        $sum_discount += $order->discount_amount;
                        $sum_vat_amount += $order->vat_amount;
                        $sum_total += $order->total;
                        $items = $order->products->count() . ' ' .
                            Str::plural('Item', $order->products->count()) . ' ' .
                            $order->products->sum('quantity') . ' ' .
                            Str::plural('Unit', $order->products->sum('quantity'));
                    @endphp
                    <tr>
                        <td class="border align-middle text-center py-1 px-2">{{ $loop->iteration }}</td>
                        <td class="border align-middle text-center py-1 px-2">{{ now()->parse($order->date)->format('d/m/Y') }}</td>
                        <td class="border align-middle text-center py-1 px-2">{{ now()->parse($order->date)->format('h:i A') }}</td>
                        <td class="border align-middle text-center py-1 px-2">{{ $order->invoice_number }}</td>
                        <td class="border align-middle text-left py-1 px-2">{{ $order->customer?->name ?? "N/A" }}</td>
                        <td class="border align-middle text-left py-1 px-2"> {{ $items }}</td>
                        <td class="border align-middle text-left py-1 px-2">{{ $order->creator?->name ?? "N/A" }}</td>
                        <td class="border align-middle text-left py-1 px-2">{{ $order->employee?->name ?? "N/A" }}</td>
                        <td class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($order->sub_total) }}</td>
                        <td class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($order->discount_amount) }}</td>
                        <td class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($order->vat_amount) }}</td>
                        <td class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($order->total) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th class="border align-bottom text-right py-1 px-4" colspan="8">Total</th>
                    <th class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($sum_sub_total) }}</th>
                    <th class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($sum_discount) }}</th>
                    <th class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($sum_vat_amount) }}</th>
                    <th class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($sum_total) }}</th>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('headContent')
<div class="">
    <table class="mt-2 mx-auto">
        <tbody>
            <tr>
                <th class="align-middle text-2xl text-center py-1 px-2" colspan="3">{{ $heading }}</th>
            </tr>
            <tr>
                <td class="w-72 align-middle text-right py-1 px-2">{{ "Duration" }}</td>
                <th class="w-4 align-middle text-center py-1 px-0"> : </th>
                <td class="w-72 align-middle text-left py-1 px-2">{{ $duration }}</td>
            </tr>
            @if($branch)
            <tr>
                <td class="align-middle text-right py-1 px-2">{{ "Branch" }}</td>
                <th class="align-middle text-center py-1 px-0"> : </th>
                <td class="align-middle text-left py-1 px-2">{{ $branch->name }}</td>
            </tr>
            @endif
            @if($customer)
            <tr>
                <td class="align-middle text-right py-1 px-2">{{ "Customer" }}</td>
                <th class="align-middle text-center py-1 px-0"> : </th>
                <td class="align-middle text-left py-1 px-2">{{ $customer->name }}</td>
            </tr>
            @endif
            @if($creator)
            <tr>
                <td class="align-middle text-right py-1 px-2">{{ "Manager" }}</td>
                <th class="align-middle text-center py-1 px-0"> : </th>
                <td class="align-middle text-left py-1 px-2">{{ $creator->name }}</td>
            </tr>
            @endif
            @if($waiter)
            <tr>
                <td class="align-middle text-right py-1 px-2">{{ "Waiter" }}</td>
                <th class="align-middle text-center py-1 px-0"> : </th>
                <td class="align-middle text-left py-1 px-2">{{ $waiter->name }}</td>
            </tr>
            @endif
            @if($payment_method)
            <tr>
                <td class="align-middle text-right py-1 px-2">{{ "Payment Method" }}</td>
                <th class="align-middle text-center py-1 px-0"> : </th>
                <td class="align-middle text-left py-1 px-2">{{ $payment_method->name }}</td>
            </tr>
            @endif
            <tr>
                <td class="align-middle text-right py-1 px-2">{{ "Total VAT" }}</td>
                <th class="align-middle text-center py-1 px-0"> : </th>
                <td class="align-middle text-left py-1 px-2">{{ App\Helpers::toAmount($sum_vat_amount) }}</td>
            </tr>
            <tr>
                <td class="align-middle text-right py-1 px-2">{{ "Total Amount" }}</td>
                <th class="align-middle text-center py-1 px-0"> : </th>
                <td class="align-middle text-left py-1 px-2">{{ App\Helpers::toAmount($sum_total) }}</td>
            </tr>

        </tbody>
    </table>

</div>
@endsection
