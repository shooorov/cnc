@extends('export')

@section('content')
<table>
    <thead>
        <tr>
			<th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Description</th>
			<th class="px-5 py-2 border-b bg-gray-100 text-xs font-bold uppercase tracking-wider text-left">Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
        <tr>
            <td align="left">{{ isset($item['name']) ? $item['name'] : "" }}</td>
            <td align="right">{{ isset($item['amount']) ? $item['amount'] : "" }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
