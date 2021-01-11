<?php

namespace App\Controller;

use App\Document\Message;
use App\Form\ContactType;
use App\Repository\DomainRepository;
use App\Repository\SiteRepository;
use App\Service\Domain\DomainResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use Mailgun\Mailgun;

class ContactController extends AbstractController
{
    private $domainResolver;

    public function __construct(DomainResolver $domainResolver)
    {
        $this->domainResolver = $domainResolver;
    }


//    public function sendMessage(Request $request, SiteRepository $siteRepository, DocumentManager $documentManager, ParameterBagInterface $params, DomainRepository $domainRepository)
    public function sendMessage(Request $request, SiteRepository $siteRepository, DocumentManager $documentManager, ParameterBagInterface $params)
    {
        /** @var Site $site */
        $site = $siteRepository->findOneBy(['host' => $this->domainResolver->extractDomainFromHost($request->getHost())]);
        $domain = $domainRepository->findOneBy(['name' => $this->domainResolver->extractDomainFromHost($request->getHost())]);
        if (null === $site && null !== $domain) {
            $site = $siteRepository->findOneBy(['domain' => $domain]);
        }

        $form = $this->createForm(ContactType::class, new Message());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Message $message */
            $message = $form->getData();
            $message->setSite($site);
            $documentManager->persist($message);
            $documentManager->flush();

            $mg = Mailgun::create($params->get('mailgun_api_key'), $params->get('mailgun_api_endpoint'));
            $mg->messages()->send($params->get('mailgun_domain'), [
                'from' => $message->getEmail(),
                'to' => $site->getUser()->getEmail(),
                'subject' => sprintf('%s %s', $message->getFirstName(), $message->getLastName()),
                'text' => $message->getMessage(),
            ]);

            return $this->json(['message' => 'ok']);
        }

        return $this->json(['error' => 'Unexpected error has occurred.']);
    }
}
