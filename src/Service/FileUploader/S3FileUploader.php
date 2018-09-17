<?php

namespace App\Service\FileUploader;

use Aws\S3\S3Client;

class S3FileUploader
{
    private $s3;
    private $bucketName;

    public function __construct($s3, $bucketName)
    {
        $this->s3 = $s3;
        $this->bucketName = $bucketName;
    }

    public function upload(string $fileName, string $fileSource): string
    {
        $this->s3->putObject(
            [
                'Bucket' => $this->bucketName,
                'Key' => $fileName,
                'SourceFile' => $fileSource,
                'ACL' => 'public-read',
            ]
        );
    }
}
