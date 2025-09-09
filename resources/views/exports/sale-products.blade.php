@extends('export')

@section('content')
<table>
    <thead>
        <tr>
			<th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
			<th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Item Name</th>
			<th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Price</th>
			<th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Sold Qty</th>
			<th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Total Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
        <tr>
			<td align="center">{{ $loop->iteration }}</td>
            <td align="center">{{ $item->name }}</td>
            <td align="center">{{ $item->rate }}</td>
            <td align="center">{{ $item->product_quantity }}</td>
            <td align="center">{{ $item->product_amount }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
