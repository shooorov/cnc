<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Food Production
    |--------------------------------------------------------------------------
    |
    | This value refer the food production feature enable for barista & chef.
    |
    */

    'production' => env('MODULE_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Central Kitchen
    |--------------------------------------------------------------------------
    |
    | This value refer the food production feature enable for barista & chef.
    |
    */

    'warehouse' => env('MODULE_WAREHOUSE', false),

    /*
    |--------------------------------------------------------------------------
    | Food Production
    |--------------------------------------------------------------------------
    |
    | This value refer the food production feature enable for barista & chef.
    |
    */

    'token' => env('MODULE_TOKEN', false),

    /*
    |--------------------------------------------------------------------------
    | Food Delivery
    |--------------------------------------------------------------------------
    |
    | This value refer the discount service feature enable
    |
    */

    'delivery' => env('MODULE_DELIVERY', false),

    /*
    |--------------------------------------------------------------------------
    | Service discount, Discount service url, Discount service name
    |--------------------------------------------------------------------------
    |
    | This value refer the discount service feature enable
    |
    */

    'discount' => env('MODULE_DISCOUNT', false),
    // 'discount_url' => env('MODULE_DISCOUNT_URL', 'http://localhost/api/member/discounts'),
    'discount_url' => env('MODULE_DISCOUNT_URL', 'https://mgt.starbase.rocks/api/member/discounts'),
    'discount_name' => env('MODULE_DISCOUNT_NAME', 'Restaurant Member Discount'),

    'check_eligibility' => env('MODULE_CHECK_ELIGIBILITY', 'https://starbase.rocks/marketing_bridge/api/check_eligibility.php'),


    /*
    |--------------------------------------------------------------------------
    | Service Chat GPT, Open AI key
    |--------------------------------------------------------------------------
    |
    | This value refer the chat gpt service feature enable
    |
    */

    'openai' => env('MODULE_OPENAI', false),
    'openai_api_key' => env('MODULE_OPENAI_API_KEY', ''),

];
