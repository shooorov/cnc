<table class="w-full">
    <tr>
        @foreach ($authorities as $key => $authority)
            @foreach ($authority as $value)
                <td class="px-0 py-2 {{ $key == 0 ? 'text-left' : 'text-right' }}">
                    <div @if ($loop->index == 0) class="border border-r-0 border-b-0 border-l-0" @endif>
                        <span>&nbsp;&nbsp;&nbsp;{{ $value }}&nbsp;&nbsp;&nbsp;</span>
                    </div>
                </td>
            @endforeach
        @endforeach
    </tr>
</table>
