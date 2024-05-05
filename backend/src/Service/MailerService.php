<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Dotenv\Dotenv;
use App\Entity\User;


class MailerService{

    public function __construct(private MailerInterface $mailer){}

     /**
     * Sends an email.
     *
     * @param string $to The recipient email address.
     * @param string $subject The subject of the email.
     * @param string $$content The content of the email.
     *
     */
    public function sendEmail(string $to,string $subject,string $content): void{
        $dotenv = new Dotenv();
        $dotenv->load('../.env');
        $mailerDsn = $_ENV['MAILER_DSN'];
        $transport=Transport::fromDsn($mailerDsn);
        $mailer=new Mailer($transport);

        $email = (new Email())
            ->from('chellettakoua@gmail.com')
            ->to($to)
            ->subject($subject)
            ->html($content);

        $mailer->send($email);
    }

    /**
     * Sends an email with content loaded from a file, where placeholders are replaced with provided attributes.
     *
     * @param string $to The recipient email address.
     * @param string $subject The subject of the email.
     * @param string $path The path to the file to be attached.
     * @param array $attributes Additional attributes to be replaced in the file content.
     *
     */
    public function sendMailFile(string $to, string $subject, string $path, array $attributes = []): void{
        $file_content=file_get_contents('../../backend/public/assets/' .$path);
        foreach ($attributes as $attribut => $value) {
            $file_content = str_replace('{{'.$attribut.'}}', $value, $file_content);
        }
        Self::sendEmail($to,$subject,$file_content);
    }

    /**
     * Sends a welcome email to a newly created account, containing a verification link.
     *
     * @param User $user The user to send the email to.
     *
     */
    public function sendAccountCreatedMail(User $user): void{

        $link = "";

        Self::sendMailFile($user->getEmail(), "Welcome to Memehub !", 'account-created.html', [
            "username" => $user->getUsername(),
            "link" => $link
        ]);
    }
    /**
     * Sends an email for password reset.
     *
     * @param User $user The user to send the email to.
     * 
     */
    public function sendPasswordResetMail(User $user): void {

        $link = "";

        Self::sendMailFile($user->getEmail(), "password reset", 'password-reset.html', [
            "username" => $user->getUsername(),
            "link" => $link
        ]);
    }

}

