<x-mail::message>
# Order need Approval ({{ $branch_name }})

Date: {{ $date->format('d/m/Y')}}

Amount: {{ $order_amount }}

<x-mail::button :url="route('order.approval', ['order_id' => $order_id, 'token' => $token])" color="success">
View Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>