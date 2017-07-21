<?php

namespace AppBundle\Service;

use Swift_Mailer;

/**
 * Created by PhpStorm.
 * User: ughostephan
 * Date: 21/07/2017
 * Time: 21:03
 */
class MailService
{

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $default_from;

    /**
     * MailService constructor.
     * @param Swift_Mailer $mailer
     * @param string|null $default_from
     */
    function __construct(Swift_Mailer $mailer, string $default_from = null)
    {
        $this->mailer = $mailer;
        $this->default_from = $default_from;
    }

    /**
     * @param string $subject
     * @param string $to
     * @param string $body
     * @param string|null $default_from
     * @throws \Exception
     */
    function sendMail(string $subject, string $to, string $body, string $default_from = null) {
        $message = (new \Swift_Message($subject))
            ->setTo($to)
            ->setBody($body, 'text/html');

        if ($default_from) {
            $message->setFrom($default_from);
        } elseif ($this->default_from) {
            $message->setFrom($this->default_from);
        } else {
            throw new \Exception("No from address for send email");
        }

        $this->mailer->send($message);
    }
}