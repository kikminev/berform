<?php

namespace App\Controller;

use App\Document\Message;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;

class ContactController extends AbstractController
{
    public function sendMessage(Request $request, DocumentManager $documentManager)
    {
        /** @var Site $site */
        $site = $documentManager->getRepository(Site::class)->findOneBy(['host' => $request->getHost()]);

        $form = $this->createForm(ContactType::class, new Message());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // todo send email

            /** @var Message $message */
            $message = $form->getData();
            $message->setSite($site);
            $message->setSite($site);
            $message->setSite($site->getUser());
            $documentManager->persist($message);
            $documentManager->flush();

            return $this->json(['message' => 'ok']);
        }

        return $this->json(['error' => 'Unexpected error has occurred.']);
    }
}
