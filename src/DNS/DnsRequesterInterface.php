<?php

namespace App\DNS;


interface DnsRequesterInterface
{
    public function addDomainDNS(string $domainName);
    public function deleteDomainDNS(string $domainZone, string $domainName);
}
