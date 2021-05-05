<?php

namespace App\DNS;

use App\Entity\Domain;
use App\Entity\UserCustomer;

interface DnsUpdater
{
    public function addDomainDNS(Domain $domain, UserCustomer $user);

    public function deleteDomainDNS(Domain $domain, UserCustomer $user);
}
