@extends('print')

@section('title', 'Requisition Items')

@section('content')
    <div class="">
        <h1 class="my-4 w-full text-xl font-bold text-center">
            {{ $heading }}
        </h1>

        <table class="w-full mb-12 border-collapse">
            <thead>
                <tr>
                    <th class="border align-middle text-center p-2" scope="col">Product</th>
                    <th class="border align-middle text-center p-2" scope="col">Quatity</th>
                    <th class="border align-middle text-center p-2" scope="col">Avg Rate</th>
                    <th class="border align-middle text-center p-2" scope="col">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requisition->products as $product)
                    <tr>
                        <td class="border align-middle text-left p-2">{{ $product->product?->name }}</td>
                        <td class="border align-middle text-center p-2">{{ $product->quantity }} {{ $product->product?->unit }}</td>
                        <td class="border align-middle text-center p-2">{{ $product->avg_rate }}</td>
                        <td class="border align-middle text-center p-2">{{ $product->total }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="border align-middle text-center p-2"></td>
                    <td class="border align-middle text-center p-2"></td>
                    <th class="border align-middle text-right p-2">Grand Total</th>
                    <th class="border align-middle text-center p-2">{{ $requisition->products->sum('total') }}</th>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
