<?php

namespace App\Service\Domain;

use App\Document\Site;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DomainResolver
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function extractDomainFromHost(string $host)
    {
        $systemDomainDiscriminator = $this->params->get('system_domain_discriminator');

        if (preg_match(sprintf('|(.*)\.%s|', $systemDomainDiscriminator), $host, $matches) && isset($matches[1])) {
            return $matches[1];
        }
        return $host;
    }

    public function getSiteReachableDomain(Site $site):RedirectResponse
    {
        if (null != $site->getDomain()) {
            return new RedirectResponse($site->getDomain()->getName());
        }

        return new RedirectResponse('http://'.$site->getHost() .'.'. $this->params->get('platform_main_domain'));
    }
}
