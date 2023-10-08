<?php

// src/Service/EmailService.php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmailFacture(  
        string $to,
        string $subject,
        string $htmlTemplate,
        array $context
    )
    {
        $from = "kplola.toviawou@ipnetinstitute.com";
        $today = Date('d M Y');
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("email_sender/$htmlTemplate") // Modèle HTML
            ->context($context); // Données passées au modèle Twig
        $this->mailer->send($email);
        //dd($email);
    }

    public function registrationVerify(
        string $to,
        string $subject,
        string $htmlTemplate,
        array $context
    )
    {
        $from = "kplola.toviawou@ipnetinstitute.com";
        $today = Date('d M Y');
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("email_sender/$htmlTemplate") // Modèle HTML
            ->context($context); // Données passées au modèle Twig
        $this->mailer->send($email);
    }
}
