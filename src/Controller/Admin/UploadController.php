<?php

namespace App\Controller\Admin;

use App\Document\Site;
use App\Repository\FileRepository;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
    public function upload(Site $site, Request $request, S3FileUploader $fileUploader, ParameterBagInterface $params)
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
        $id = $this->attachFileToSite($site, $url, $newFileName, $file->getSize());

        $url = $params->get('resource_provider_domain').$newFileName.'?h=150&w=150&fit=crop&border-radius=10';

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
        $i = 0;
        foreach ($files as $file) {
            $order = $file->getOrder();
            if(!isset($orderedFiles[$order])) {
                $orderedFiles[$order] = $file;
            } else {
                $orderedFiles[] = $file;
            }
        }
        ksort($orderedFiles);

        return $orderedFiles;
    }

    public static function getOrderedFilesIdsConcatenated(array $files): string
    {
        $fileConcatenated = '';
        foreach ($files as $file) {
            $fileConcatenated .= $file->getId() . ';';
        }

        return $fileConcatenated;
    }

    private function attachFileToSite(Site $site, $url, $baseName, $size)
    {
        $this->denyAccessUnlessGranted('modify', $site);

        $file = new File();
        $file->setSite($site);
        $file->setFileUrl($url);
        $file->setBaseName($baseName);
        $file->setUser($this->getUser());
        $file->setSize($size);

        $this->documentManager->persist($file);
        $this->documentManager->flush($file);

        return $file->getId();
    }

    private function generateUniqueFileName()
    {
        return md5(uniqid(false, true));
    }
}
