@extends('print')

@section('title', 'Kitchen Delivery Items')

@section('content')
    <div class="">
        <h1 class="my-4 w-full text-xl font-bold text-center">
            {{ $heading }}
        </h1>

        <table class="w-full mb-12 border-collapse">
            <thead>
                <tr>
                    <th class="border align-middle text-center p-2" scope="col">Product</th>
                    <th class="border align-middle text-center p-2" scope="col">Requisition Quantity</th>
                    <th class="border align-middle text-center p-2" scope="col">Delivery Quantity</th>
                    <th class="border align-middle text-center p-2" scope="col">Avg Rate</th>
                    <th class="border align-middle text-center p-2" scope="col">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kitchen_delivery->items as $item)
                    <tr>
                        <td class="border align-middle text-left p-2">{{ $item->item?->name }}</td>
                        <td class="border align-middle text-center p-2">{{ $item->requisition_quantity }} {{ $item->item?->unit }}</td>
                        <td class="border align-middle text-center p-2">{{ $item->delivery_quantity }} {{ $item->item?->unit }}</td>
                        <td class="border align-middle text-center p-2">{{ $item->avg_rate }}</td>
                        <td class="border align-middle text-center p-2">{{ $item->total }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="border align-middle text-center p-2"></td>
                    <td class="border align-middle text-center p-2"></td>
                    <td class="border align-middle text-center p-2"></td>
                    <th class="border align-middle text-right p-2">Grand Total</th>
                    <th class="border align-middle text-center p-2">{{ $kitchen_delivery->items->sum('total') }}</th>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
