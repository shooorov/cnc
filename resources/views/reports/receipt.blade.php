@extends('print')

@section('title', 'Cash Receipt ' . $order->invoice_number)

@section('content')
    <div class="divide-y divide-dashed">
        <h1 class="mb-4 w-full text-2xl font-extrabold text-center"> {{ $company_name }} </h1>
        <p class="mb-4 w-full text-base font-extrabold text-center"> {{ $company_address }} </p>

        <table class="border-collapse w-full mb-12">
            <tbody>
                <tr>
                    <th class="align-middle text-left py-1" scope="col"># Order</th>
                    <td class="align-middle text-right py-1" scope="col">{{ $order->invoice_number }}</td>
                </tr>
                <tr>
                    <th class="align-middle text-left py-1" scope="col"># Time</th>
                    <td class="align-middle text-right py-1" scope="col">{{ $order->date->format('Y-m-d h:i a') }}</td>
                </tr>
                <tr>
                    <th class="align-middle text-left py-1" scope="col"># Manager</th>
                    <td class="align-middle text-right py-1" scope="col">{{ $order->creator?->name }}</td>
                </tr>
                <tr>
                    <th class="align-middle text-left py-1" scope="col" colspan="2"># Customer :
                        {{ $order->customer?->name }} - {{ $order->customer?->mobile }}</th>
                </tr>
                @if ($order->delivery_address)
                    <tr>
                        <th class="align-middle text-left py-1" scope="col" colspan="2"># Delivery Address :
                            {{ $order->delivery_address }}</th>
                    </tr>
                @endif
            </tbody>
        </table>

        <table class="border-collapse w-full mb-12">
            <thead>
                <tr>
                    <th class="align-middle text-center py-2" scope="col">#</th>
                    <th class="align-middle text-left py-2" scope="col">Product</th>
                    <th class="align-middle text-center py-2" scope="col">Qty</th>
                    <th class="align-middle text-center py-2" scope="col">Rate</th>
                    <th class="align-middle text-right py-2" scope="col">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $iteration = 0;
                @endphp
                @foreach ($order->products as $product)
                    @php
                        $iteration++;
                    @endphp
                    <tr>
                        <td class="align-middle text-center py-2">{{ $iteration }}</td>
                        <td class="align-middle text-left py-2">{{ $product->product->name }}</td>
                        <td class="align-middle text-center py-2">{{ $product->quantity }}</td>
                        <td class="align-middle text-center py-2">{{ $product->rate }}</td>
                        <td class="align-middle text-right py-2">{{ $product->total }}</td>
                    </tr>
                @endforeach
                @foreach ($order->products as $product)
                    @foreach ($product->toppings as $prod_topping)
                        @php
                            $iteration++;
                        @endphp
                        <tr>
                            <td class="align-middle text-center py-2">{{ $iteration }}</td>
                            <td class="align-middle text-left py-2">{{ $prod_topping->topping->title }}</td>
                            <td class="align-middle text-center py-2">{{ $prod_topping->quantity }}</td>
                            <td class="align-middle text-center py-2">{{ $prod_topping->rate }}</td>
                            <td class="align-middle text-right py-2">{{ $prod_topping->total }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <table class="border-collapse w-full mb-5">
            <tbody>
                @if ($order->delivery_cost)
                    <tr>
                        <td class="align-middle text-xs font-bold text-left py-1" colspan="4">Delivery Cost</td>
                        <td class="align-middle text-xs font-bold text-right py-1">{{ $order->delivery_cost }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="align-middle text-xs font-bold text-left py-1" colspan="4">Sub Total</td>
                    <td class="align-middle text-xs font-bold text-right py-1">{{ $order->sub_total }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-xs font-bold text-left py-1" colspan="4">Discount</td>
                    <td class="align-middle text-xs font-bold text-right py-1">{{ $order->discount_amount }}</td>
                </tr>
				@if($order->service_cost > 0)
                <tr>
                    <td class="align-middle text-xs font-bold text-left py-1" colspan="4">Service Cost</td>
                    <td class="align-middle text-xs font-bold text-right py-1">{{ $order->service_cost }}</td>
                </tr>
				@endif
                <tr>
                    <td class="align-middle text-xs font-bold text-left py-1" colspan="4">VAT</td>
                    <td class="align-middle text-xs font-bold text-right py-1">{{ $order->vat_amount }}</td>
                </tr>
                <tr>
                    <td class="align-middle text-2xl font-bold text-left py-1" colspan="4">Total:</td>
                    <td class="align-middle text-xs font-bold text-right py-1">{{ $order->total }}</td>
                </tr>
                @if ($order->cash)
                    <tr>
                        <td class="align-middle text-xs font-bold text-left py-1" colspan="4">Cash</td>
                        <td class="align-middle text-xs font-bold text-right py-1">{{ $order->cash }}</td>
                    </tr>
                @endif
                @if ($order->change)
                    <tr>
                        <td class="align-middle text-xs font-bold text-left py-1" colspan="4">Change</td>
                        <td class="align-middle text-xs font-bold text-right py-1">{{ $order->change }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <h1 class="mb-4 w-full text-xs font-bold text-center"> Thank You </h1>
    </div>
@endsection
