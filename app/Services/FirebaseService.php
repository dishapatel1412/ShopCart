<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Contract\Messaging;

class FirebaseService
{
    protected Messaging $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(
                base_path(env('FIREBASE_CREDENTIALS'))
            );

        $this->messaging = $factory->createMessaging();
    }

    // public function sendNotification(string $deviceToken, string $title, string $body)
    // {
    //     try {
    //         $message = CloudMessage::new()
    //         ->toToken($deviceToken)
    //         ->withNotification(
    //             Notification::create($title, $body)
    //         );

    //         $this->messaging->send($message);

    //         return true;
    //     } catch (\Exception $e) {
    //         \Log::error($e->getMessage());

    //         return false;
    //     }
    // }

    public function sendNotification(
        string $deviceToken,
        string $title,
        string $body
    ) {
        try {

            \Log::info('Firebase notification started');

            \Log::info($deviceToken);

            $message = CloudMessage::new()
                ->toToken($deviceToken)
                ->withNotification(
                    Notification::create(
                        $title,
                        $body
                    )
                );

            $result = $this->messaging->send($message);

            // \Log::info('Firebase sent successfully');

            // \Log::info(json_encode($result));

            sleep(2);

            return true;

        } catch (\Exception $e) {

            \Log::error('Firebase Error');

            \Log::error($e->getMessage());

            dd($e->getMessage());

            return false;
        }
    }
}