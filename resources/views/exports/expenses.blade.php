@extends('export')

@section('content')
<table>
    <thead>
        <tr>
			<th class="w-12 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-center">S.N.</th>
			<th class="w-40 px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Date</th>
			<th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Account</th>
			<th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Category</th>
			<th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-right">Amount</th>
		</tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
        <tr>
			<td align="center">{{ $loop->iteration }}</td>
			<td align="center">{{ $item->datetime_format }}</td>
			<td align="center">{{ isset($accounts[$item->account_id]) ? $accounts[$item->account_id] : "N/A" }}</td>
			<td align="center">{{ isset($categories[$item->expense_category_id]) ? $categories[$item->expense_category_id] : "N/A" }}</td>
			<td align="center">{{ $item->amount }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
