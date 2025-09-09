@extends('print')

@section('title', $heading)

@section('content')
    <div class="mb-8">
        <table class="table-fixed w-full mt-8 border-collapse">
            <thead>
                <tr>
                    <th class="w-16 border align-middle text-center py-1 px-2" scope="col">S.N.</th>
                    <th class="w-32 border align-middle text-center py-1 px-2" scope="col">Date</th>
                    <th class="border align-middle text-center py-1 px-2" scope="col">Sub Total</th>
                    <th class="border align-middle text-center py-1 px-2" scope="col">Discount</th>
                    <th class="border align-middle text-center py-1 px-2" scope="col">Total</th>
                    <th class="border align-middle text-center py-1 px-2" scope="col">VAT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sum_subtotal = 0;
					$sum_discount_amount = 0;
					$sum_total = 0;
					$sum_vat = 0;
                @endphp
                @foreach ($durations as $day)
                    @php
						$total = $day['sub_total'] - $day['discount_amount'];
                        $sum_subtotal += $day['sub_total'];
                        $sum_discount_amount += $day['discount_amount'];
						$sum_total += $total;
                        $sum_vat += $day['vat'];
                    @endphp
                    <tr>
                        <td class="border align-middle text-center py-1 px-2">{{ $loop->iteration }}</td>
                        <td class="border align-middle text-center py-1 px-2">{{ $day['date'] }}</td>
                        <td class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($day['sub_total']) }}</td>
                        <td class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($day['discount_amount']) }}</td>
                        <td class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($total) }}</td>
                        <td class="border align-middle text-right py-1 px-2">{{ App\Helpers::toAmount($day['vat']) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th class="border align-bottom text-right py-1 px-4" colspan="2">Total</th>
                    <th class="border align-middle text-right py-1 px-2">
                        {{ App\Helpers::toAmount($sum_subtotal) }}
                    </th>
                    <th class="border align-middle text-right py-1 px-2">
                        {{ App\Helpers::toAmount($sum_discount_amount) }}
                    </th>
                    <th class="border align-middle text-right py-1 px-2">
                        {{ App\Helpers::toAmount($sum_total) }}
                    </th>
                    <th class="border align-middle text-right py-1 px-2">
                        {{ App\Helpers::toAmount($sum_vat) }}
                    </th>
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
                    <td class="align-middle text-center py-1 px-2" colspan="3">{{ $bin_number }}</td>
                </tr>
                <tr>
                    <th class="w-72 align-middle text-right py-1 px-2">{{ 'Duration' }}</th>
                    <th class="w-4 align-middle text-center py-1 px-0"> : </th>
                    <td class="w-72 align-middle text-left py-1 px-2">{{ $date_duration }}</td>
                </tr>
                {{-- <tr>
                    <th class="align-middle text-right py-1 px-2">{{ 'Total Sub Total' }}</th>
                    <th class="align-middle text-center py-1 px-0"> : </th>
                    <td class="align-middle text-left py-1 px-2">{{ App\Helpers::toAmount($sum_subtotal) }}
                    </td>
                </tr>
                <tr>
                    <th class="align-middle text-right py-1  px-2">{{ 'Total Discount' }}</th>
                    <th class="align-middle text-center py-1 px-0"> : </th>
                    <td class="align-middle text-left py-1 px-2">{{ App\Helpers::toAmount($sum_discount_amount) }}
                    </td>
                </tr>
                <tr>
                    <th class="align-middle text-right py-1 px-2">{{ 'Total Sale' }}</th>
                    <th class="align-middle text-center py-1 px-0"> : </th>
                    <td class="align-middle text-left py-1 px-2">{{ App\Helpers::toAmount($sum_total) }}
                    </td>
                </tr>
                <tr>
                    <th class="align-middle text-right py-1 px-2">{{ 'Total VAT' }}</th>
                    <th class="align-middle text-center py-1 px-0"> : </th>
                    <th class="align-middle text-left py-1 px-2">{{ App\Helpers::toAmount($sum_vat) }}
                    </th>
                </tr> --}}
            </tbody>
        </table>

    </div>
@endsection
