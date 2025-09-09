<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderApproval extends Mailable
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

        return $this->subject("Order need Approval - $app_name ($branch_name)")->markdown('emails.orders.approval', $params);
    }
}
