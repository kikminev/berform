<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Service\FileUploader\S3FileUploader;

class UploadController extends AbstractController
{
    const MAX_ALLOWED_SIZE = 2000000;

    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function upload(Request $request, S3FileUploader $fileUploader)
    {
        $filesBag = $request->files->get('files');

        $file = $filesBag[0];

        if(self::MAX_ALLOWED_SIZE < $file->getSize()) {
            // do something
        }

        $fileUploader->upload();

        // todo:
        //  verify that it's an image
        //  create thumb
        //  upload to s3
        //

        \Doctrine\Common\Util\Debug::dump($file); exit;
    }
}
