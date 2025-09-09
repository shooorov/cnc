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
                    <th class="border align-middle text-center p-2" scope="col">Item</th>
                    <th class="border align-middle text-center p-2" scope="col">Quatity</th>
                    <th class="border align-middle text-center p-2" scope="col">Avg Rate</th>
                    <th class="border align-middle text-center p-2" scope="col">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requisition->items as $item)
                    <tr>
                        <td class="border align-middle text-center p-2">{{ $item->item?->name }}</td>
                        <td class="border align-middle text-center p-2">{{ $item->quantity }} {{ $item->item?->unit }}</td>
                        <td class="border align-middle text-center p-2">{{ $item->avg_rate }}</td>
                        <td class="border align-middle text-center p-2">{{ $item->total }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="border align-middle text-center p-2"></td>
                    <td class="border align-middle text-center p-2"></td>
                    <td class="border align-middle text-center p-2">Grand Total</td>
                    <td class="border align-middle text-center p-2">{{ $requisition->items->sum('total') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
