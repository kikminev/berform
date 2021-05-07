<?php

namespace App\DNS;

use App\Entity\Domain;
use App\Entity\UserCustomer;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\HttpClient\HttpClient;

class CloudflareDnsUpdater implements DnsUpdater
{
    private string $hostAPIUrl;
    private string $clientAPIUrl;
    private string $cloudflareHostApiKey;
    private string $passwordSalt;
    private EntityManagerInterface $entityManager;

    public function __construct(
        string $hostAPIUrl,
        string $clientAPIUrl,
        string $cloudflareHostApiKey,
        string $passwordSalt,
        EntityManagerInterface $entityManager
    ) {
        $this->hostAPIUrl = $hostAPIUrl;
        $this->clientAPIUrl = $clientAPIUrl;
        $this->cloudflareHostApiKey = $cloudflareHostApiKey;
        $this->passwordSalt = $passwordSalt;
        $this->entityManager = $entityManager;
    }

    public function createCloudflareAccount(UserCustomer $user): bool
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

        $this->entityManager->flush();

        return true;
    }

    public function addDomainDNS(Domain $domain, UserCustomer $user): bool
    {
        $client = HttpClient::create();
        $domainName = $domain->getName();

        $cloudFlareApiKey = $user->getCloudflareApiKey();
        $cloudFlareUserKey = $user->getCloudflareUserKey();

        if (null === $cloudFlareApiKey || null === $cloudFlareUserKey) {
            throw new RuntimeException('Domain zone can\'t be created without valid account');
        }

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

            if($response->getStatusCode() == 200) {
                $result = json_decode($response->getContent());
                $zoneId = $result->result->id;

                $ns1 = $result->result->name_servers[0];
                $ns2 = $result->result->name_servers[1];

                $domain->setCloudflareZoneId($zoneId);
                $domain->setNS1($ns1);
                $domain->setNS2($ns2);

                $params = json_encode(['type' => 'A', 'name' => $domainName, 'content' => '54.77.174.96', 'ttl' => 1]);

                $client->request('POST',
                    $this->clientAPIUrl . sprintf('/client/v4/zones/%s/dns_records', $zoneId),
                    [
                        'body' => $params,
                        'headers' => [
                            'X-Auth-Email' => $user->getEmail(),
                            'X-Auth-Key' => $cloudFlareApiKey,
                            'Content-Type' => 'application/json',
                        ],
                    ]
                );

                $this->entityManager->persist($domain);
                $this->entityManager->flush();

                return true;
            }

            return false;
    }

    /**
     * Creates DNS records for specific domain by using the Cloudflare zoneId for this domain.
     *
     * 1. adds A record
     * 2. adds A record for www
     * 3. setup of page rules for cache everything of the whole site. e.g. it caches www.domain.com entirely
     */
    public function createDnsRecords($domainName, $zoneId)
    {
    }

    public function deleteDomainDNS(Domain $domain, UserCustomer $user)
    {
    }

    private function getPassword(string $userName)
    {
        return '_1' . md5($userName . $this->passwordSalt); //turn the array into a string
    }
}
