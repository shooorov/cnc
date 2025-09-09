@php
    $isLandscape = isset($options['orientation']) && $options['orientation'] == 'L' ? true : false;
    // @dd($isLandscape)
@endphp
<table class="w-full text-base">
    <tbody>
        <tr>
            <td class="w-48 align-top text-left py-2 pl-0" rowspan="{{ $isLandscape ? 2 : 1 }}">{{ $reference ?? "" }}</td>
            <th class="align-middle text-lg text-center px-3 py-0">{{ App\Helpers::getSettingValueOf('company_name') }}</th>
            <td class="w-48 align-top text-right py-2 pr-0" rowspan="{{ $isLandscape ? 2 : 1 }}">Page {PAGENO} of {nb}</td>
        </tr>
        <tr>
            <td class="align-middle text-center px-3" colspan="{{ $isLandscape ? 1 : 3 }}">{!! App\Helpers::getSettingValueOf('company_address') !!}</td>
        </tr>
    </tbody>
</table>
