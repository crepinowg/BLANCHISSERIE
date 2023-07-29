<?php

// src/Service/EmailService.php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail()
    {
        $email = (new Email())
            ->from(new Address('totopressing@gmail.com'))
            ->to('crepintoviawou@gmail.com')
            ->subject('FACTURE')
            ->text('HI BOYS');

        $this->mailer->send($email);
    }
}
