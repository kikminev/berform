<?php

namespace App\DNS;

use App\Document\Domain;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use RuntimeException;
use Symfony\Component\HttpClient\HttpClient;

class CloudflareDnsUpdater implements DnsUpdater
{
    private string $hostAPIUrl;
    private string $clientAPIUrl;
    private string $cloudflareHostApiKey;
    private string $passwordSalt;
    private DocumentManager $documentManager;

    public function __construct(
        string $hostAPIUrl,
        string $clientAPIUrl,
        string $cloudflareHostApiKey,
        string $passwordSalt,
        DocumentManager $documentManager
    ) {
        $this->hostAPIUrl = $hostAPIUrl;
        $this->clientAPIUrl = $clientAPIUrl;
        $this->cloudflareHostApiKey = $cloudflareHostApiKey;
        $this->passwordSalt = $passwordSalt;
        $this->documentManager = $documentManager;
    }

    public function createCloudflareAccount(User $user): bool
    {
        $email = $user->getEmail();
        $client = HttpClient::create();
        $response = $client->request('POST',
            $this->hostAPIUrl,
            [
                'body' => [
                    'cloudflare_email' => $email,
                    'cloudflare_pass' => $this->getPassword($email),
                    'unique_id' => $user->getId(),
                    'act' => 'user_create',
                    'host_key' => $this->cloudflareHostApiKey,
                ],
            ]);

        if (!is_string($response->getContent())) {
            return false;
        }

        $result = json_decode($response->getContent());

        if (!isset($result->result) || $result->result !== 'success') {
            return false;
        }

        $user->setCloudflareUserKey($result->response->user_key);
        $user->setCloudflareApiKey($result->response->user_api_key);

        $this->documentManager->flush();

        return true;
    }

    public function addDomainDNS(Domain $domain, User $user): bool
    {
        $client = HttpClient::create();
        $domainName = $domain->getName();

        $cloudFlareApiKey = $user->getCloudflareApiKey();
        $cloudFlareUserKey = $user->getCloudflareUserKey();

        if (null === $cloudFlareApiKey || null === $cloudFlareUserKey) {
            throw new RuntimeException('Domain zone can\'t be created without valid account');
        }

        try {
            $response = $client->request('POST',
                $this->clientAPIUrl . '/client/v4/zones',
                [
                    'body' => sprintf('{"name":"%s"}', $domainName),
                    'headers' => [
                        'X-Auth-Email' => $user->getEmail(),
                        'X-Auth-Key' => $cloudFlareApiKey,
                        'Content-Type' => 'application/json',
                    ],
                ]
            );

            $result = json_decode($response->getContent());

            $zoneId = $result->result->id;
            $ns1 = $result->result->name_servers[0];
            $ns2 = $result->result->name_servers[1];

            $domain->setCloudflareZoneId($zoneId);
            $domain->setNS1($ns1);
            $domain->setNS2($ns2);

            $this->documentManager->flush($domain);

            return true;

        } catch (Exception $exception) {
        }

        return false;
    }

    /**
     * Creates DNS records for specific domain by using the Cloudflare zoneId for this domain.
     *
     * 1. adds A record
     * 2. adds A record for www
     * 3. setup of page rules for cache everything of the whole site. e.g. it caches www.domain.com entirely
     * 4. setup for page rule for exclusion of www.domain.com/en/c which is the dynamic address that should not be cached
     */
    public function createDnsRecords($domainName, $zoneId)
    {
        // todo: needs to be implemented
    }

    public function deleteDomainDNS(Domain $domain, User $user)
    {
    }

    private function getPassword(string $userName)
    {
        return '_1' . md5($userName . $this->passwordSalt); //turn the array into a string
    }
}
