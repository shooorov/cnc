@extends('export')

@section('content')
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
			<th class="w-24 border align-middle text-center py-1 px-2" scope="col">Created At</th>
			<th class="w-24 border align-middle text-center py-1 px-2" scope="col">Updated At</th>
			<th class="w-24 border align-middle text-center py-1 px-2" scope="col">Modified After</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($orders as $order)
			@php
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
				<td class="border align-middle text-right py-1 px-2">{{ $order->created_at->format('d/m/Y h:i A') }}</td>
				<td class="border align-middle text-right py-1 px-2">{{ $order->updated_at->format('d/m/Y h:i A') }}</td>
				<td class="border align-middle text-right py-1 px-2">{{ $order->updated_at->diff($order->created_at)->format('%H:%I:%S') }}</td>
			</tr>
		@endforeach
	</tbody>
</table>
@endsection
