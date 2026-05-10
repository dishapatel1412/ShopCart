<?php

namespace App\Services;

use Twilio\Rest\Client;

class SmsService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    public function sendOtp($number, $otp)
    {
        try {
            // dd($number);
            $message = "Your OTP is $otp";

            $this->client->messages->create(
                $number, // MUST be in +91 format
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message,
                ]
            );
            // \Log::info($number);

            return true;

        } catch (\Exception $e) {
            \Log::error('Twilio Error: ' . $e->getMessage());
            return false;
        }
    }
}