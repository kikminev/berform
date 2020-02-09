<?php

namespace App\Controller\Admin;

use App\Document\Site;
use App\Repository\FileRepository;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Service\FileUploader\S3FileUploader;
use Symfony\Component\HttpFoundation\File\File as FileUpload;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Document\File;

class UploadController extends AbstractController
{
    const MAX_ALLOWED_SIZE = 2000000;

    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @param Site $site
     * @param Request $request
     * @param S3FileUploader $fileUploader
     * @return JsonResponse
     * @throws \Exception
     */
    public function upload(Site $site, Request $request, S3FileUploader $fileUploader)
    {
        $filesBag = $request->files->get('files');

        /** @var FileUpload $file */
        $file = $filesBag[0];

        if (self::MAX_ALLOWED_SIZE < $file->getSize()) {
            // todo: throw error
            throw new \Exception('error');
        }
        //todo: create thumbnail

        $newFileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
        $url = $fileUploader->upload($newFileName, $file);
        $id = $this->attachFileToSite($site, $url, $newFileName);

        return new JsonResponse(['url' => $url, 'id' => $id]);
    }

    public function deleteFile(File $file, DocumentManager $documentManager)
    {
        $this->denyAccessUnlessGranted('edit', $file);

        $file->setDeleted(true);
        $documentManager->flush();

        return new JsonResponse(['message' => 'ok']);
    }


    public static function getOrderedFiles(array $files): array
    {
        $orderedFiles = [];
        foreach ($files as $file) {
            $orderedFiles[$file->getOrder()] = $file;
        }
        ksort($orderedFiles);

        return $orderedFiles;
    }

    private function attachFileToSite(Site $site, $url, $baseName)
    {
        $this->denyAccessUnlessGranted('edit', $site);

        $file = new File();
        $file->setSite($site);
        $file->setFileUrl($url);
        $file->setBaseName($baseName);
        $file->setUser($this->getUser());

        $this->documentManager->persist($file);
        $this->documentManager->flush($file);

        return $file->getId();
    }

    private function generateUniqueFileName()
    {
        return md5(uniqid(false, true));
    }
}
