<?php

namespace App\Service\FileUploader;

use Aws\S3\S3Client;

class FileUploader
{
    private $s3;
    public function __construct($s3)
    {
        $this->s3 = $s3;
    }

    public function upload()
    {
        $result = $this->s3->listBuckets();
        \Doctrine\Common\Util\Debug::dump($result); exit;
    }
}