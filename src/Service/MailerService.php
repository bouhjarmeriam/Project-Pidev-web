<?php

// src/Service/MailerService.php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendAdminNotification($subject, $body)
    {
        $email = (new Email())
            ->from('cryptomonnaie95@gmail.com') // L'adresse de l'expéditeur
            ->to('bouhjarmariem012@gmail.com') // L'adresse de l'administrateur
            ->subject($subject)
            ->html($body); // Le corps de l'email au format HTML

        $this->mailer->send($email);
    }
}
