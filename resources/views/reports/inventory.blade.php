@extends('print')

@section('title', 'Inventories')

@section('content')
    <div class="">
        <h1 class="my-4 w-full text-xl font-bold text-center">
            {{ $heading }} <br> {{ $duration }}
        </h1>

        <table class="w-full mb-12 border-collapse">
            <thead>
                <tr>
                    <th class="border align-middle text-center p-2" scope="col">Date</th>
                    <th class="border align-middle text-center p-2" scope="col">Item</th>
                    <th class="border align-middle text-center p-2" scope="col">Quatity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($item_inventories as $item_inventory)
                    @foreach ($item_inventory->items as $item)
                        <tr>
                            <td class="border align-middle text-center p-2">{{ $item_inventory->date->format('d/m/Y') }}
                            </td>
                            <td class="border align-middle text-center p-2">
                                {{ $item->item ? $item->item?->name : $item->product?->name }}</td>
                            <td class="border align-middle text-center p-2">
                                {{ number_format($item->quantity) . ' ' . $item->unit?->short . ' ' . $item->direction }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
