<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Dotenv\Dotenv;


class MailerService{

    public function __construct(private MailerInterface $mailer){}
    public function sendEmail(
        $to='taki74ayadi@gmail.com',
        $content='<p>See Twig integration for better HTML integration!</p>',
        $subject='Time for Symfony Mailer!'
    ): void
    {
        $dotenv = new Dotenv();
        $dotenv->load('../.env');
        // Get the MAILER_DSN from the environment
        $mailerDsn = $_ENV['MAILER_DSN'];
        $transport=Transport::fromDsn($mailerDsn);
        $mailer=new Mailer($transport);

        $email = (new Email())
            ->from('chellettakoua@gmail.com')
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text('Sending emails is fun again!')
            ->html($content);

        $mailer->send($email);
    }
}

