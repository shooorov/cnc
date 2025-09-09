<?php

namespace App;

use Illuminate\Support\Facades\Log;

class ShortMessage
{
    private static $url = '';

    private static $params = [];

    public function send(): array
    {
        $url = self::$url;
        $params = self::$params;

        $response = json_encode(['status' => 'FAILED']);

        try {
            $response = self::callApi($url, json_encode($params));
        } catch (\Throwable $th) {
            Log::error("Single sms sending error: {$params['message']} to {$params['msisdn']}");
        }

        return json_decode($response);
    }

    public static function single($msisdn, $message, $uuid)
    {
        self::$url = config('sms.domain').'/api/v3/send-sms';

        self::$params = [
            'api_token' => config('sms.api_token'),
            'sid' => config('sms.sid'),
            'msisdn' => $msisdn,
            'sms' => $message,
            'csms_id' => $uuid,
        ];

        return new self;
    }

    public static function bulk($msisdns, $message, $uuid)
    {
        self::$url = config('sms.domain').'/api/v3/send-sms/bulk';

        self::$params = json_encode([
            'api_token' => config('sms.api_token'),
            'sid' => config('sms.sid'),
            'msisdn' => $msisdns,
            'sms' => $message,
            'batch_csms_id' => $uuid,
        ]);

        return new self;
    }

    public static function dynamic($messageData)
    {
        // [
        // 	'msisdn' => $item['msisdn'],
        // 	'text' => $item['text'],
        // 	'csms_id' => $item['uuid'],
        // ]

        self::$url = config('sms.domain').'/api/v3/send-sms/dynamic';

        self::$params = json_encode([
            'api_token' => config('sms.api_token'),
            'sid' => config('sms.sid'),
            'sms' => $messageData,
        ]);

        return new self;
    }

    private function callApi($url, $params)
    {
        Log::info($params);

        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: '.strlen($params),
            'accept:application/json',
        ]);

        $response = curl_exec($ch);
        Log::info($response);

        curl_close($ch);

        return $response;
    }
}
