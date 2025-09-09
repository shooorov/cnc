<x-mail::message>
# Daily Sale Summary ({{ $branch_name }})
Date: {{ $date->format('d/m/Y')}}

<x-mail::table>
| SALE SUMMARY                      |                          |
| :------------                     |                 --------:|
@foreach($cards_summary as $card)
| {{ Str::of($card['name']) }}      | {{$card['amount']}}      |
@endforeach
</x-mail::table>
 
<x-mail::table>
| RECIPE WISE SUMMARY               |                          |
| :------------                     |                 --------:|
@foreach($cards_recipe as $card)
| {{ Str::of($card['name']) }}      | {{$card['amount']}}      |
@endforeach
</x-mail::table>
 
<x-mail::table>
| DINE TYPE WISE SUMMARY            |                          |
| :------------                     |                 --------:|
@foreach($cards_dine_type as $card)
| {{ Str::of($card['name']) }}      | {{$card['amount']}}      |
@endforeach
</x-mail::table>
 
<x-mail::table>
| PAYMENT METHOD WISE SUMMARY       |                          |
| :------------                     |                 --------:|
@foreach($cards_payment_method as $card)
| {{ Str::of($card['name']) }}      | {{$card['amount']}}      |
@endforeach
</x-mail::table>
 
Thanks,<br>
{{ config('app.name') }}
</x-mail::message>