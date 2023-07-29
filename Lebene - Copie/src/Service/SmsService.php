<?php

// src/Service/SmsService.php
namespace App\Service;

use Twilio\Rest\Client;

class SmsService
{
    private $twilioClient;

    public function __construct(string $accountSid, string $authToken)
    {
        $this->twilioClient = new Client($accountSid, $authToken);
    }

    public function sendSms(string $to, string $from, string $message)
    {
        $this->twilioClient->messages->create(
            $to,
            [
                'from' => $from,
                'body' => $message,
            ]
        );
    }
}
