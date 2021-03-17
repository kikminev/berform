<?php


namespace App\Mail\MailTransmitter;

use App\Mail\Envelope;

interface MailTransmitterInterface
{
    public function transmit(Envelope $envelope);
}
