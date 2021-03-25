<?php

namespace App\Mail;

use App\Mail\MailTransmitter\Mailgun\MailgunTransmitter;
use App\Mail\MailTransmitter\MailTransmitterInterface;

class Sender
{
    private MailTransmitterInterface $transmitter;

    public function __construct(MailgunTransmitter $mailGunTransmitter)
    {
        $this->transmitter = $mailGunTransmitter;
    }

    public function send(Message $message)
    {
        $this->transmitter->transmit($message);
    }
}
