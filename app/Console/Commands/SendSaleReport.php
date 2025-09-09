<?php

namespace App\Console\Commands;

use App\Data;
use App\Helpers;
use App\Mail\SaleReport;
use App\Models\Branch;
use App\Models\EmailDigest;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSaleReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:report {op_date?} {--debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail about sale report to users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $op_date = $this->argument('op_date');
		$debug = $this->option('debug');

		$date = $op_date ? now()->parse($op_date) : now()->subDay();

        foreach ($branches as $branch) {
            $user_ids = EmailDigest::where('is_checked', true)->where('branch_id', $branch->id)->pluck('user_id');
            $user_emails = User::whereIn('id', $user_ids)->get()->pluck('email')->toArray();

            $recipients = array_unique($user_emails);

            $end_date = Helpers::dayEndedAt($date);
            $start_date = Helpers::dayStartedAt($date);

			$cards_summary = Data::whereBranch($branch->id)->whereBetween($start_date, $end_date)->summaryCards();
			$cards_dine_type = Data::whereBranch($branch->id)->whereBetween($start_date, $end_date)->dineTypeCards();
			$cards_recipe = Data::whereBranch($branch->id)->whereBetween($start_date, $end_date)->recipeCards();
			$cards_payment_method = Data::whereBranch($branch->id)->whereBetween($start_date, $end_date)->paymentMethodCards();

			$params = [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'date' => $start_date,
				'cards_summary' => $cards_summary,
				'cards_recipe' => $cards_recipe,
				'cards_dine_type' => $cards_dine_type,
				'cards_payment_method' => $cards_payment_method,
            ];

            // if ($debug) {
            //     Mail::to('arif.saiket@gmail.com')->queue(new SaleReport($params));
            //     Log::info($branch->name.' branch sales report sent --debug');

            //     continue;
            // }
            if (count($recipients) > 0) {
                foreach ($recipients as $recipient) {
                    Log::info($recipient.' getting mail from '.$branch->name.' branch');
                }

                // Mail::to($recipients)->bcc(['arif.saiket@gmail.com'])->queue(new SaleReport($params));
                Mail::to($recipients)->queue(new SaleReport($params));

                Log::info($branch->name.' branch sales report sent');
            }

        }

        return 0;
    }
}
