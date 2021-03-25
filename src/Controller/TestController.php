<?php

namespace App\Controller;

use App\Mail\Message;
use App\Mail\Sender;
use Http\Discovery\Exception\NotFoundException;
use Mailgun\Mailgun;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TestController extends AbstractController
{
    public function test(ParameterBagInterface $parameterBag, Sender $sender)
    {
        throw new NotFoundException();

        $message = $this->render('Mail/Payment/successful_payment.html.twig', ['invoice_pdf' => 'https://invoice.stripe.com/i/acct_1I1XREEhLqLirlZw/invst_J7AlbFECXpt7eLM78rlqVp562MP9CZG'])->getContent();

        $messageTEst = new Message('no-reply@berform.com', 'kokominev@yahoo.com', $message);
        $sender->send($messageTEst);

        echo 'dada12'; exit;

        $mg = Mailgun::create($parameterBag->get('mailgun_api_key'), $parameterBag->get('mailgun_api_endpoint'));
        $mg->messages()->send($parameterBag->get('mailgun_domain'),
            [
                'from' => 'no-reply@berform.com',
                'to' => 'kokominev@yahoo.com',
                'subject' => sprintf('%s', 'Kik Minev'),
                'html' => $message,
            ]);

        return $this->json(['message' => 'ok']);
    }
}
