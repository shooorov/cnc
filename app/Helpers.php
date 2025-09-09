<?php

namespace App;

use App\Http\Cache\CacheSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Helpers
{
    public static function greeting()
    {
        $hours = date('H');

        $greeting = $hours < '12' ? 'Good Morning' : '';
        $greeting .= $hours >= '12' && $hours < '15' ? 'Good Noon' : '';
        $greeting .= $hours >= '15' && $hours < '17' ? 'Good Afternoon' : '';
        $greeting .= $hours >= '17' && $hours < '19' ? 'Good Evening' : '';
        $greeting .= $hours >= '19' ? 'Good Night' : '';

        return $greeting;
    }

    public static function setNameTitle()
    {
        $ignore_models = [
            // 'Account',
        ];

        $filter_models = [
            // 'Account',
        ];

        $tables = UseDatabase::tables();
        foreach ($tables as $table) {
            $file_path = base_path("app\Models\\$table.php");

            if (! file_exists($file_path) || in_array($table, $ignore_models) || ! in_array($table, $filter_models)) {
                continue;
            }

            $model = "App\Models\\".$table;
            $model = new $model;
            $table_columns = Schema::getColumnListing($table);
            if (! in_array('name', $table_columns)) {
                continue;
            }
            foreach ($model::all() as $item) {
                $item->name = Str::of($item->name)->title();
                $item->save();
            }
        }
    }

    public static function getSettingValueOf($item, $isPlural = false)
    {
        $settings = CacheSetting::get()->pluck('value', 'name')->toArray();

        if (isset($settings[$item])) {
            return $isPlural ? Str::plural($settings[$item]) : $settings[$item];
        } else {
            return null;
        }
    }

    public static function dayStartedAt($date = null)
    {
        if (! $date) {
            $date = now();
		}
        
		$time = self::getSettingValueOf('day_started_at') ?? '00:00';
		
		if (is_string($date)) {
			return now()->parse($date . " " . $time);
		}

		$start_time = $date->format('Y-m-d') . ' ' . $time;
        $started_at = now()->parse($start_time);
        //THIS UNCOMMENT FOR DATE (WITHOUT THIS, END DATE IS AFTER 1 DAY))
        $started_at = $date->lt($started_at) ? $started_at->subDay() : $started_at;

        return $started_at;
    }

    public static function dayStartedAt2($date = null)
    {
        if (! $date) {
            $date = now();
		}
        
		$time = self::getSettingValueOf('day_started_at') ?? '00:00';
		
		if (is_string($date)) {
			return now()->parse($date . " " . $time);
		}

		$start_time = $date->format('Y-m-d') . ' ' . $time;
        $started_at = now()->parse($start_time);

        //$started_at = $date->lt($started_at) ? $started_at->subDay() : $started_at;

        return $started_at;
    }


    


    // ---------------day started issue trying to fix-------------------------------------------------
    public static function dayStartedAt_new($date = null)
    {
        if (! $date) {
            $date = now();
        }

        $time = self::getSettingValueOf('day_started_at') ?? '00:00';

        // Combine the provided date with the start time
        $start_time = $date instanceof Carbon ? $date->format('Y-m-d') . ' ' . $time : $date . " " . $time;
        $started_at = now()->parse($start_time);

        // Adjust only if the given date is before the start time, not considering the time part
        if ($date instanceof Carbon) {
            $dateWithoutTime = $date->copy()->startOfDay();
            $started_at = $dateWithoutTime->lt($started_at) ? $started_at->subDay() : $started_at;
        }

        return $started_at;
    }


    public static function dayEndedAt($date = null)
    {
       
        return self::dayStartedAt($date)->addDay()->subMicrosecond();
    }

    

    public static function operationDay($date = null)
    {
        return self::dayStartedAt($date)->format('Y-m-d');
    }

    

    public static function trans($number)
    {
        $en = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
        $bn = ['১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯', '০'];

        if (App::getLocale() == 'en') {
            $number = str_replace($bn, $en, $number);
        } else {
            $number = str_replace($en, $bn, $number);
        }

        return $number;
    }

    public static function toAmount($number, $format = 'bn')
    {
        $sign = '';
        if ($number < 0) {
            $sign = '-';
            $number = $number * -1;
        }

        $data_string = strval($number);
        $data_array = explode('.', $data_string);

        $value = $data_array[0];
        $value = strrev($value);
        $decimal = Str::padRight($data_array[1] ?? 0, 2, '0');
        $number_array = [];

        $digits = [3, 2, 2];
        $i = 0;

        do {
            $digit = $digits[$i % 3];
            $number_array[] = substr($value, 0, $digit);
            $value = substr($value, $digit);
            $i++;
        } while (strlen($value) > 0);

        $rev_value = implode(',', $number_array);
        $value = strrev($rev_value);
        $decimal = substr($decimal, 0, 2);

        return $sign.($value ?? '0').'.'.$decimal;
    }

    public static function toNumber($unfiltered_number)
    {
        return filter_var($unfiltered_number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    public static function toQuantity($number)
    {
        return substr(self::toAmount($number), 0, -3);
    }

    public static function toWords($main_price)
    {
        $string = '';
        $price = intval($main_price);
        $length = strlen((string) $price);

        if ($length > 7) {
            $string .= intval($price / 10000000) > 0 ? self::numberToWords(intval($price / 10000000)) . ' ' . __('Crore').' ' : '';
            $price = $price % 10000000;

            $length = strlen((string) $price);
            $price = floatval($price);
        }

        if ($length > 5) {
            $string .= intval($price / 100000) > 0 ? self::numberToWords(intval($price / 100000)) . ' ' . __('Lac').' ' : '';
            $price = $price % 100000;

            $length = strlen((string) $price);
            $price = floatval($price);
        }

        if ($length > 3) {
            $string .= intval($price / 1000) > 0 ? self::numberToWords(intval($price / 1000)) . ' ' . __('Thousand').' ' : '';
            $price = $price % 1000;
        }

        if ($length > 2) {
            $string .= intval($price / 100) > 0 ? self::numberToWords(intval($price / 100)) . ' ' . __('Hundred').' ' : '';
            $price = $price % 100;
        }

        $string .= self::numberToWords($price);
        $string .= ' Taka ';

        $amount = explode('.', $main_price);
        if (count($amount) > 1 && floatval($amount[1]) > 0) {
            $string .= ' & ';
            $string .= self::numberToWords($amount[1]);
            $string .= ' Paisa ';
        }

        $string .= ' Only';

        return self::trans(trim($string));
    }

    public static function numberToWords($price)
    {
        $ones = [0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'];
        $tens = [0 => '', 1 => '', 2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty', 6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'];

        $string = '';
        $value = floatval($price);
        if ($value > 19) {
            $string .= $value / 10 > 1 ? $tens[intval($value / 10)].' ' : '';
            $value = $value % 10;
        } else {
            $string .= $value / 10 > 0 ? $ones[intval($value / 1)].' ' : '';
            $value = $value > 0 ? $value % $value : 0;
        }
        $string .= $ones[intval($value / 1)];

        return $string;
    }
}
