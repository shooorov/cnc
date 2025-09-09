<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SettingSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:setting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Setting::updateOrCreate([
            'type' => 'text',
            'name' => 'token_value',
            'title' => 'Token Value',
            'value' => '5',
        ]);

        Artisan::call('optimize:clear');
    }
}
