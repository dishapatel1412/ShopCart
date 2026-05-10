<?php

namespace App\Services;

use Twilio\Rest\Client;

class WhatsappMessageService
{
    protected $client, $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->from = config('services.twilio.whatsapp_from');
    }

    public function sendMessage($to, $message, $mediaUrl = null)
    {
        // $sid = getenv("TWILIO_SID");
        // $token = getenv("TWILIO_AUTH_TOKEN");
        // $twilio = new Client($sid, $token);

        try {
            if (!str_starts_with($to, '+')) {
                $to = '+91' . $to;
            }

            \Log::info($to);
            \Log::info($message);

            $data = [
                'from' => "whatsapp:{$this->from}",
                'body' => $message,
            ];

            if ($mediaUrl) {
                $data['mediaUrl'] = [$mediaUrl];
            }

            $response = $this->client->messages->create(
                "whatsapp:$to",
                $data
            );

            \Log::info('WhatsApp sent successfully');
            \Log::info($response->sid);

        } catch (\Exception $e) {

            \Log::error('Twilio Error: ' . $e->getMessage());

        }
    }
}