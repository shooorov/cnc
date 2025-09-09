@extends('print')

@section('title', 'Inventories')

@section('content')
    <div class="">
        <h1 class="my-4 w-full text-xl font-bold text-center">
            {{ $heading }}
        </h1>

        <h1 class="my-4 w-full text-lg font-bold text-center">
            Referenct: {{ $product_inventory->id }}
        </h1>
        <h1 class="my-4 w-full text-lg font-bold text-center">
            Date: {{ now()->parse($product_inventory->date)->format('d/m/Y h:i A') }}
        </h1>

        <table class="w-full mb-12 border-collapse">
            <thead>
                <tr>
                    <th class="border align-middle text-center p-2" scope="col">{{ $product_name }}</th>
                    <th class="border align-middle text-center p-2" scope="col">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($product_inventory->products as $item)
                    <tr>
                        <td class="border align-middle text-center p-2">
                            {{ $item->product?->name }}
                        </td>
                        <td class="border align-middle text-center p-2">
                            {{ $item->quantity }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h1 class="my-4 w-full text-xl font-bold text-center">
            {{ $item_name }}
        </h1>

        <table class="w-full mb-12 border-collapse">
            <thead>
                <tr>
                    <th class="border align-middle text-center p-2" scope="col">#</th>
                    <th class="border align-middle text-center p-2" scope="col">{{ $item_name }}</th>
                    <th class="border align-middle text-center p-2" scope="col">Total Required</th>
                    <th class="border align-middle text-center p-2" scope="col">Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($required_items as $item)
                    <tr>
                        <td class="border align-middle text-center p-2">
                            {{ $loop->iteration }}
                        </td>
                        <td class="border align-middle text-center p-2">
                            {{ $item['item_name'] }}
                        </td>
                        <td class="border align-middle text-center p-2">
                            {{ $item['quantity'] }}
                        </td>
                        <td class="border align-middle text-center p-2">
                            {{ $item['unit'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
