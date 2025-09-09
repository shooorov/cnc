<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UseDatabase
{
    public static function tables()
    {
        $database = env('DB_DATABASE');
        $tables = array_column(Schema::getAllTables(), "Tables_in_$database");
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        return $tables;
    }
}
