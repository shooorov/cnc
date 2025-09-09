<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\ShortMessage;
use Illuminate\Console\Command;

class SendShortMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:sms';

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
        $messageData = [];

        $messages = Message::whereIsSent(false)->get();

        foreach ($messages as $message) {
            if (! $message->customer) {
                continue;
            }

            $messageData[] = [
                'msisdn' => $message->contact,
                'text' => $message->content,
            ];
        }

        if (count($messageData) > 0) {
            ShortMessage::dynamic($messageData)->send();
        }

        return 0;
    }
}
