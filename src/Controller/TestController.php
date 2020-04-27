<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\DNS\CloudflareDnsRequester;
use Symfony\Component\HttpClient\HttpClient;

class TestController extends AbstractController
{
    public function test(CloudflareDnsRequester $cloudflareDnsRequester)
    {

        $cloudflareDnsRequester->addDomainDNS('kik.com');

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.github.com/repos/symfony/symfony-docs');

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        print_r($content);
        exit;
    }
}
