<?php


namespace App\Mail\MailTransmitter\Mailgun;


use App\Mail\Envelope;
use App\Mail\MailTransmitter\MailTransmitterInterface;
use Mailgun\Mailgun;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailgunTransmitter implements MailTransmitterInterface
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function transmit(Envelope $envelope)
    {
        $mailgunClient = Mailgun::create($this->parameterBag->get('mailgun_api_key'), $this->parameterBag->get('mailgun_api_endpoint'));
        return $mailgunClient->messages()->send($this->parameterBag->get('mailgun_domain'),
            [
                'from' => $envelope->getFrom(),
                'to' => $envelope->getTo(),
                'subject' => sprintf('%s', 'Kik Minev'),
                'html' => $envelope->getContent(),
            ]);
    }
}
