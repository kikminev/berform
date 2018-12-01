<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Service\FileUploader\S3FileUploader;
use Symfony\Component\HttpFoundation\File\File as FileUpload;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Document\File;
use App\Document\Page;

class UploadController extends AbstractController
{
    const MAX_ALLOWED_SIZE = 2000000;

    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function upload(Page $page, Request $request, S3FileUploader $fileUploader)
    {
        $filesBag = $request->files->get('files');

        /** @var FileUpload $file */
        $file = $filesBag[0];

        if (self::MAX_ALLOWED_SIZE < $file->getSize()) {
            // todo: throw error
        }
         //todo: create thumbnail

        $newFileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
        $url = $fileUploader->upload($newFileName, $file);
        $id = $this->attachFileToPage($page, $url);

        return new JsonResponse(['url' => $url, 'id' => $id]);
    }

    private function attachFileToPage($page, $url)
    {
        $file = new File();
        $file->setPage($page);
        $file->setFileUrl($url);

        $this->documentManager->persist($file);
        $this->documentManager->flush($file);

        return $file->getId();
    }

    private function generateUniqueFileName()
    {
        return md5(uniqid(false, true));
    }

    // todo: delete file
}
