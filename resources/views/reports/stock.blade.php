@extends('print')

@section('title', $heading)

@section('content')
    <div class="">
        <h1 class="my-4 w-full text-xl font-bold text-center"> {{ $heading }} </h1>

        <table class="w-full mb-12 border-collapse">
            <thead>
                <tr>
                    <th class="border align-middle text-center p-2" scope="col">#</th>
                    <th class="border align-middle text-center p-2" scope="col">Item</th>
                    <th class="border align-middle text-center p-2" scope="col">Quatity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td class="border align-middle text-center py-1 px-2">{{ $loop->iteration }}</td>
                        <td class="border align-middle text-center py-1 px-2">{{ $item['name'] }}</td>
                        <td class="border align-middle text-center py-1 px-2">{{ $item['quantity'] }} {{ $item['unit'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
