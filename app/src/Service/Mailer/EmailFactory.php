<?php


namespace App\Service\Mailer;

use App\Message\EmailNotification;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailFactory
{
    public const SUBJECT_PREFIX = '[Vue Symfony Starter] ';

    /**
     * @var string
     */
    private $mailerFrom;

    public function __construct(string $mailerFrom)
    {
        $this->mailerFrom = $mailerFrom;
    }

    public function createEmailFromMessage(EmailNotification $email): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from($email->sender() ?? $this->mailerFrom)
            ->to($email->recipient())
            ->subject(EmailFactory::SUBJECT_PREFIX . $email->subject())
            ->htmlTemplate('emails/'.$email->template().'.html.twig')
            ->textTemplate('emails/'.$email->template().'.txt.twig')
            ->context($email->payload());
    }

    public function createEmailFromData(
        string $recipient,
        string $subject,
        array $payload,
        string $template,
        ?string $from = null
    ) {
        return (new TemplatedEmail())
            ->from($from ?? $this->mailerFrom)
            ->to($recipient)
            ->subject(EmailFactory::SUBJECT_PREFIX . $subject)
            ->htmlTemplate('emails/'.$template.'.html.twig')
            ->textTemplate('emails/'.$template.'.txt.twig')
            ->context($payload);
    }
}
