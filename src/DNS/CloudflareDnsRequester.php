<?php

namespace App\DNS;

class CloudflareDnsRequester implements DnsRequesterInterface
{
    public function addDomainDNS(string $domainName)
    {
        echo $domainName;
    }

    public function deleteDomainDNS(string $domainZone, string $domainName)
    {
    }
}
