<?php

namespace App\Service\DNS;


interface ConnectorInterface
{
    public function createAccount();
    public function createZone();
    public function deleteAccount();
    public function deleteZone();
}
