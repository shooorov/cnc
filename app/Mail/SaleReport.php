<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SaleReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private $params)
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $params = $this->params;
        $app_name = config('app.name');
        $branch_name = $params['branch_name'];
        $date = $params['date'];

        return $this->subject("Daily Sales Report - {$date->format('jS F, Y')} - $app_name ($branch_name)")->markdown('emails.sales.report', $params);
    }
}
