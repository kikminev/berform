<?php

namespace App\Service\FileUploader;

use Aws\S3\S3Client;

class S3FileUploader
{
    private $s3Client;
    private $bucketName;

    public function __construct(S3Client $s3Client, $bucketName)
    {
        $this->s3Client = $s3Client;
        $this->bucketName = $bucketName;
    }

    public function upload(string $fileName, string $fileSource): string
    {
        $awsResult = $this->s3Client->putObject(
            [
                'Bucket' => $this->bucketName,
                'Key' => $fileName,
                'SourceFile' => $fileSource,
                'ACL' => 'public-read',
            ]
        );

        return $awsResult->get('ObjectURL');
    }
}
