@extends('print')

@section('title', $heading)

@section('content')
	@php
		$total = 0;
	@endphp
	@foreach ($employees as $employee)
    <div class="mb-8">
        <h1 class="mb-2 text-xl">{{ $employee->name }}</h1>
        <table class="table-fixed w-full mt-8 border-collapse">
            <thead>
                <tr>
                    <th class="w-16 border align-middle text-center py-1 px-2" scope="col">S.N.</th>
                    <th class="w-24 border align-middle text-center py-1 px-2" scope="col">Date</th>
                    <th class="w-24 border align-middle text-center py-1 px-2" scope="col">Time</th>
                    <th class="border align-middle text-center py-1 px-2" scope="col">Number</th>
                    <th class="border align-middle text-center py-1 px-2" scope="col">Customer</th>
                    <th class="border align-middle text-center py-1 px-2" scope="col">Note</th>
                    <th class="w-24 border align-middle text-center py-1 px-2" scope="col">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
				$sum_total = 0;
                @endphp
                @foreach ($employee->services as $service)
                    @php
					$total_amount = $service->order_product?->total;
					$served_persons = $service->order_product?->employees?->count() ?? 1;
					
					$service->amount = $total_amount / $served_persons;
					$sum_total += $service->amount
                    @endphp
                    <tr>
                        <td class="border align-middle text-center py-1 px-2">{{ $loop->iteration }}</td>
                        <td class="border align-middle text-center py-1 px-2">{{ now()->parse($service->created_at)->format('d/m/Y') }}</td>
                        <td class="border align-middle text-center py-1 px-2">{{ now()->parse($service->created_at)->format('h:i A') }}</td>
                        <td class="border align-middle text-center py-1 px-2">{{ $service->order?->invoice_number }}</td>
                        <td class="border align-middle text-left py-1 px-2">{{ $service->order?->customer?->name ?? "N/A" }}</td>
                        <td class="border align-middle text-left py-1 px-2">{{ $service->subject ?? "N/A" }}</td>
                        <td class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($service->amount) }}</td>
                    </tr>
                @endforeach
				@php
				$total += $sum_total;
				@endphp
                <tr>
                    <th class="border align-bottom text-right py-1 px-4" colspan="6">Total</th>
                    <th class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($sum_total) }}</th>
                </tr>
            </tbody>
        </table>
    </div>
	@endforeach
@endsection

@section('headContent')
<div class="">
    <table class="mt-2 mx-auto">
        <tbody>
            <tr>
                <th class="align-middle text-2xl text-center py-1 px-2" colspan="3">{{ $heading }}</th>
            </tr>
            <tr>
                <td class="w-72 align-middle text-lg text-right py-1 px-2">{{ "Duration" }}</td>
                <th class="w-4 align-middle text-lg text-center py-1 px-0"> : </th>
                <td class="w-72 align-middle text-lg text-left py-1 px-2">{{ $duration }}</td>
            </tr>
            <tr>
                <td class="align-middle text-lg text-right py-1 px-2">{{ "Total Amount" }}</td>
                <th class="align-middle text-lg text-center py-1 px-0"> : </th>
                <td class="align-middle text-lg text-left py-1 px-2">{{ App\Helpers::toAmount($total) }}</td>
            </tr>

        </tbody>
    </table>

</div>
@endsection
