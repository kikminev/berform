<?php

namespace App\Service\DomainResolver;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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
}
