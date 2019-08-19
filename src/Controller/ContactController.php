<?php

namespace App\Controller;

use App\Document\Message;
use App\Form\ContactType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use Mailgun\Mailgun;

class ContactController extends AbstractController
{
    public function sendMessage(Request $request, DocumentManager $documentManager, ParameterBagInterface $params)
    {
        /** @var Site $site */
        $site = $documentManager->getRepository(Site::class)->findOneBy(['host' => $request->getHost()]);

        $form = $this->createForm(ContactType::class, new Message());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Message $message */
            $message = $form->getData();
            $message->setSite($site);
            $documentManager->persist($message);
            $documentManager->flush();

            //echo '>>>'.$params->get('MAILGUN_API_KEY'); exit;
            //print_r($params); exit;

            $mg = Mailgun::create($params->get('MAILGUN_API_KEY'));
            $mg->messages()->send($params->get('MAILGUN_DOMAIN'), [
                'from'    => $message->getEmail(),
                'to'      => $site->getUser()->getEmail(),
                'subject' => sprintf('%s %s', $message->getFirstName(), $message->getLastName()),
                'text'    => $message->getMessage()
            ]);

            return $this->json(['message' => 'ok']);
        }

        return $this->json(['error' => 'Unexpected error has occurred.']);
    }
}
