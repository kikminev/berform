<?php


namespace App\Mail\MailTransmitter\Mailgun;


use App\Mail\Message;
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

    public function transmit(Message $message)
    {
        $mailgunClient = Mailgun::create($this->parameterBag->get('mailgun_api_key'), $this->parameterBag->get('mailgun_api_endpoint'));
        return $mailgunClient->messages()->send($this->parameterBag->get('mailgun_domain'),
            [
                'from' => $message->getFrom(),
                'to' => $message->getTo(),
                'subject' => sprintf('%s', $message->getSubject()),
                'html' => $message->getContent(),
            ]);
    }
}
