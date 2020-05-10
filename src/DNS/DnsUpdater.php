<?php

namespace App\DNS;


use App\Document\Domain;
use App\Document\User;

interface DnsUpdater
{
    public function addDomainDNS(Domain $domain, User $user);
    public function deleteDomainDNS(Domain $domain, User $user);
}
