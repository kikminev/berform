<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Node;
use App\Entity\Site;
use App\Form\Admin\AlbumType;
use App\Repository\AlbumRepository;
use App\Repository\PageRepository;
use App\Repository\FileRepository;
use App\Repository\ShotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AlbumController extends AbstractController
{
    private $entityManager;

    // todo: import repositories with auto-wiring
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function list(
        Request $request,
        Site $site,
        AlbumRepository $albumRepository,
        ShotRepository $shotRepository,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $nodes = $albumRepository->getActiveByUserSite($this->getUser(), $site);

        return $this->render(
            'Admin/Node/node_list.html.twig',
            [
                'nodes' => $nodes,
                'site' => $site,
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $id
     * @param FileRepository $fileRepository
     * @param ParameterBagInterface $param
     * @return Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function edit(
        Request $request,
        string $id,
        FileRepository $fileRepository,
        AlbumRepository $albumRepository,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var Album $album */
        $album = $albumRepository->findOneBy(['userCustomer' => $this->getUser(), 'id' => $id]);
        $site = $album->getSite();
        $supportedLanguages = array_filter($param->get('supported_languages'), function ($language) use ($site) {
            return in_array($language, $site->getSupportedLanguages(), false);
        });

        $form = $this->createForm(AlbumType::class, $album, ['supported_languages' => $supportedLanguages]);

        if (null === $album) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new Exception('Error');
        }

        $this->denyAccessUnlessGranted('modify', $album);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $currentTranslatedTitles = $album->getTranslatedTitle();
            $currentTranslatedContent = $album->getTranslatedContent();
            $currentTranslatedKeywords = $album->getTranslatedKeywords();
            $currentTranslatedMetaDescription = $album->getTranslatedMetaDescription();
            foreach ($supportedLanguages as $language) {
                $translatedTitle = isset($currentTranslatedTitles[$language]) ? $currentTranslatedTitles[$language] : '';
                $translatedContent = isset($currentTranslatedContent[$language]) ? $currentTranslatedContent[$language] : '';
                $translatedKeywords = isset($currentTranslatedKeywords[$language]) ? $currentTranslatedKeywords[$language] : '';
                $translatedMetaDescription = isset($currentTranslatedMetaDescription[$language]) ? $currentTranslatedMetaDescription[$language] : '';
                $form->get('title_' . $language)->setData($translatedTitle);
                $form->get('content_' . $language)->setData($translatedContent);
                $form->get('keywords_' . $language)->setData($translatedKeywords);
                $form->get('meta_description_' . $language)->setData($translatedMetaDescription);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $updatedTranslatedTitle = [];
            $updatedTranslatedContent = [];
            $updatedTranslatedKeywords = [];
            $updatedTranslatedMetaDescription = [];
            foreach ($supportedLanguages as $language) {
                $updatedTranslatedTitle[$language] = $form['title_' . $language]->getData();
                $updatedTranslatedContent[$language] = $form['content_' . $language]->getData();
                $updatedTranslatedKeywords[$language] = $form['keywords_' . $language]->getData();
                $updatedTranslatedMetaDescription[$language] = $form['meta_description_' . $language]->getData();
            }

            $album->setTranslatedTitle($updatedTranslatedTitle);
            $album->setTranslatedContent($updatedTranslatedContent);
            $album->setTranslatedKeywords($updatedTranslatedKeywords);
            $album->setTranslatedMetaDescription($updatedTranslatedMetaDescription);

            $attachedFiles = $request->request->get('album')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = array_filter(explode(';', $attachedFiles));

                $nodeFiles = $fileRepository->getActiveByIds($attachedFilesIds, $this->getUser());
                foreach ($nodeFiles as $attachedFile) {
                    $album->addFile($attachedFile);
                }

                if(!empty($nodeFiles) && null === $album->getDefaultImage()) {
                    $defaultImage = array_keys($nodeFiles)[0];
                    $album->setDefaultImage($nodeFiles[$defaultImage]);
                }
            }
            $this->entityManager->persist($album);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_admin_album_list', [
                'site' => $album->getSite()->getId(),
                'type' => 'album',
            ]);
        }

        $orderedFiles = UploadController::getOrderedFiles($album->getFiles()->toArray());
        $form->get('attachedFiles')->setData(UploadController::getOrderedFilesIdsConcatenated($orderedFiles));

        return $this->render(
            'Admin/Node/node_edit.html.twig',
            [
                'form' => $form->createView(),
                'files' => $orderedFiles,
                'supportedLanguages' => $supportedLanguages,
                'site' => $album->getSite(),
                'node' => $album,
            ]
        );
    }

    public function create(
        Request $request,
        Site $site,
        FileRepository $fileRepository,
        ParameterBagInterface $param
    ): Response {
        $this->denyAccessUnlessGranted('modify', $site);
        $supportedLanguages = array_filter($param->get('supported_languages'), function ($language) use ($site) {
            return in_array($language, $site->getSupportedLanguages(), false);
        });

        $node = new Album();
        $form = $this->createForm(AlbumType::class, $node, ['supported_languages' => $supportedLanguages]);
        $editTemplate = 'Admin/Node/node_edit.html.twig';

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $currentTranslatedTitles = $node->getTranslatedTitle();
            $currentTranslatedContent = $node->getTranslatedContent();
            $currentTranslatedKeywords = $node->getTranslatedKeywords();
            $currentTranslatedMetaDescription = $node->getTranslatedMetaDescription();
            foreach ($supportedLanguages as $language) {
                $translatedTitle = isset($currentTranslatedTitles[$language]) ? $currentTranslatedTitles[$language] : '';
                $translatedContent = isset($currentTranslatedContent[$language]) ? $currentTranslatedContent[$language] : '';
                $translatedKeywords = isset($currentTranslatedKeywords[$language]) ? $currentTranslatedKeywords[$language] : '';
                $translatedMetaDescription = isset($currentTranslatedMetaDescription[$language]) ? $currentTranslatedMetaDescription[$language] : '';

                if($form->has('title_' . $language)) {
                    $form->get('title_' . $language)->setData($translatedTitle);
                }

                if($form->has('content_' . $language)) {
                    $form->get('content_' . $language)->setData($translatedContent);
                }
                if ($form->has('keywords_' . $language)) {
                    $form->get('keywords_' . $language)->setData($translatedKeywords);
                }

                if($form->has('meta_description_' . $language)) {
                    $form->get('meta_description_' . $language)->setData($translatedMetaDescription);
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $updatedTranslatedTitle = [];
            $updatedTranslatedContent = [];
            $updatedTranslatedKeywords = [];
            $updatedTranslatedMetaDescription = [];
            foreach ($supportedLanguages as $language) {
                $updatedTranslatedTitle[$language] =  isset($form['title_' . $language]) ? $form['title_' . $language]->getData() : null;
                $updatedTranslatedContent[$language] = isset($form['content_' . $language]) ? $form['content_' . $language]->getData() : null;
                $updatedTranslatedKeywords[$language] = isset($form['keywords_' . $language]) ? $form['keywords_' . $language]->getData() : null;
                $updatedTranslatedMetaDescription[$language] = isset($form['meta_description_' . $language]) ? $form['meta_description_' . $language]->getData() : null;
            }

            $node->setTranslatedTitle($updatedTranslatedTitle);
            $node->setTranslatedContent($updatedTranslatedContent);
            $node->setTranslatedKeywords($updatedTranslatedKeywords);
            $node->setTranslatedMetaDescription($updatedTranslatedMetaDescription);
            $node->setSite($site);
            $node->setUserCustomer($this->getUser());

            $attachedFiles = $request->request->get('album')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = array_filter(explode(';', $attachedFiles));

                $nodeFiles = $fileRepository->getActiveByIds($attachedFilesIds, $this->getUser());
                foreach ($nodeFiles as $attachedFile) {
                    $node->addFile($attachedFile);
                }

                if(!empty($nodeFiles) && null === $node->getDefaultImage()) {
                    $defaultImage = array_keys($nodeFiles)[0];
                    $node->setDefaultImage($nodeFiles[$defaultImage]);
                }
            }

            $this->entityManager->persist($node);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_admin_album_list', [
                'site' => $node->getSite()->getId(),
                'type' => 'album',
            ]);
        }

        return $this->render(
            $editTemplate,
            [
                'site' => $site,
                'form' => $form->createView(),
                'supportedLanguages' => $supportedLanguages,
                'node' => $node,
            ]
        );
    }
}