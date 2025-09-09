<?php

namespace App;

use App\Models\ItemInventory;
use App\Models\ProductInventory;

class Formula
{
    public static function nextOrderNumber()
    {
        return date('Y-m-d H:i:s');
    }

    public static function nextItemInventoryNumber()
    {
        return ItemInventory::withTrashed()->select('serial')->distinct()->count() + 1;
    }

    public static function nextProductInventoryNumber()
    {
        return ProductInventory::withTrashed()->select('serial')->distinct()->count() + 1;
    }
}
