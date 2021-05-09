<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Site;
use App\Form\ContactType;
use App\Repository\DomainRepository;
use App\Repository\SiteRepository;
use App\Service\Domain\DomainResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Mailgun\Mailgun;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactController extends AbstractController
{
    private $domainResolver;
    private TranslatorInterface $translator;

    public function __construct(DomainResolver $domainResolver, TranslatorInterface $translator)
    {
        $this->domainResolver = $domainResolver;
        $this->translator = $translator;
    }

    public function sendMessage(
        Request $request,
        SiteRepository $siteRepository,
        ParameterBagInterface $params,
        DomainRepository $domainRepository,
        EntityManagerInterface $entityManager
    ) {
        /** @var Site $site */
        $site = $siteRepository->findOneBy(['host' => $this->domainResolver->extractDomainFromHost($request->getHost())]);
        $domain = $domainRepository->findOneBy(['name' => $this->domainResolver->extractDomainFromHost($request->getHost())]);
        if (null === $site && null !== $domain) {
            $site = $domain->getSite();
        }

        $form = $this->createForm(ContactType::class, new Message());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Message $message */
            $message = $form->getData();
            $message->setSite($site);
            $message->setUserCustomer($site->getUserCustomer());

            $entityManager->persist($message);
            $entityManager->flush();

            $mg = Mailgun::create($params->get('mailgun_api_key'), $params->get('mailgun_api_endpoint'));
            $mg->messages()->send($params->get('mailgun_domain'),
                [
                    'from' => $message->getEmail(),
                    'to' => $site->getUserCustomer()->getEmail(),
                    'subject' => sprintf('%s %s', $message->getFirstName(), $message->getLastName()),
                    'text' => $message->getMessage(),
                ]);

            return $this->json(['message' => $this->translator->trans('user_site_contact_us_message_sent')]);
        }

        return $this->json(['error' => 'Unexpected error has occurred.']);
    }
}
