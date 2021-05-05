<?php


namespace App\Mail\MailTransmitter;

use App\Mail\Message;

interface MailTransmitterInterface
{
    public function transmit(Message $envelope);
}
