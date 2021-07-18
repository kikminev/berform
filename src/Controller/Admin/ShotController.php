<?php

namespace App\Controller\Admin;

use App\Entity\Shot;
use App\Entity\Site;
use App\Form\Admin\ShotType;
use App\Repository\PageRepository;
use App\Repository\FileRepository;
use App\Repository\ShotRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShotController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function list(
        Request $request,
        Site $site,
        ShotRepository $shotRepository,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $nodes = $shotRepository->getActiveByUserSite($this->getUser(), $site);

        return $this->render(
            'Admin/Shot/shot_list.html.twig',
            [
                'nodes' => $nodes,
                'site' => $site,
            ]
        );
    }

    public function edit(
        Request $request,
        Shot $shot,
        FileRepository $fileRepository,
        ShotRepository $shotRepository,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $site = $shot->getSite();
        $supportedLanguages = array_filter($param->get('supported_languages'), function ($language) use ($site) {
            return in_array($language, $site->getSupportedLanguages(), false);
        });
        $form = $this->createForm(ShotType::class, $shot, ['supported_languages' => $supportedLanguages]);

        if (null === $shot) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new Exception('Error');
        }

        $this->denyAccessUnlessGranted('modify', $shot);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $currentTranslatedContent = $shot->getTranslatedContent();
            foreach ($supportedLanguages as $language) {
                $translatedContent = isset($currentTranslatedContent[$language]) ? $currentTranslatedContent[$language] : '';

                if($form->has('content_' . $language)) {
                    $form->get('content_' . $language)->setData($translatedContent);
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $updatedTranslatedContent = [];
            foreach ($supportedLanguages as $language) {
                $updatedTranslatedContent[$language] = $form['content_' . $language]->getData();
            }

            $shot->setTranslatedContent($updatedTranslatedContent);

            $attachedFiles = $request->request->get('shot')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = array_filter(explode(';', $attachedFiles));

                $shotFiles = $fileRepository->findActiveByIds($attachedFilesIds, $this->getUser());
                foreach ($shotFiles as $attachedFile) {
                    $shot->addFile($attachedFile);
                }

                if(!empty($shotFiles) && null === $shot->getDefaultImage()) {
                    $defaultImage = array_keys($shotFiles)[0];
                    $shot->setDefaultImage($shotFiles[$defaultImage]);
                }
            }
            $this->entityManager->persist($shot);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_admin_shot_list', [
                'site' => $shot->getSite()->getId(),
            ]);
        }


        $files = $fileRepository->findAllActiveByShotAndSite($shot, $shot->getSite());
        $orderedFiles = UploadController::getOrderedFiles($files);
        $form->get('attachedFiles')->setData(UploadController::getOrderedFilesIdsConcatenated($orderedFiles));

        return $this->render(
            'Admin/Shot/shot_edit.html.twig',
            [
                'form' => $form->createView(),
                'files' => $orderedFiles,
                'supportedLanguages' => $supportedLanguages,
                'site' => $shot->getSite(),
                'node' => $shot,
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

        $shot = new Shot();
        $shot->setUpdatedAt(new DateTime());
        $shot->setCreatedAt(new DateTime());
        $form = $this->createForm(ShotType::class, $shot, ['supported_languages' => $supportedLanguages]);
        $editTemplate = 'Admin/Shot/shot_edit.html.twig';

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $currentTranslatedTitles = $shot->getTranslatedTitle();
            $currentTranslatedContent = $shot->getTranslatedContent();
            $currentTranslatedKeywords = $shot->getTranslatedKeywords();
            $currentTranslatedMetaDescription = $shot->getTranslatedMetaDescription();
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

            $shot->setTranslatedTitle($updatedTranslatedTitle);
            $shot->setTranslatedContent($updatedTranslatedContent);
            $shot->setTranslatedKeywords($updatedTranslatedKeywords);
            $shot->setTranslatedMetaDescription($updatedTranslatedMetaDescription);
            $shot->setSite($site);
            $shot->setUserCustomer($this->getUser());

            $attachedFiles = $request->request->get('shot')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = array_filter(explode(';', $attachedFiles));

                $nodeFiles = $fileRepository->findActiveByIds($attachedFilesIds, $this->getUser());
                foreach ($nodeFiles as $attachedFile) {
                    $shot->addFile($attachedFile);
                }

                if(!empty($nodeFiles) && null === $shot->getDefaultImage()) {
                    $defaultImage = array_keys($nodeFiles)[0];
                    $shot->setDefaultImage($nodeFiles[$defaultImage]);
                }
            }

            $this->entityManager->persist($shot);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_admin_shot_list', [
                'site' => $shot->getSite()->getId(),
                'type' => 'album',
            ]);
        }

        return $this->render(
            $editTemplate,
            [
                'site' => $site,
                'form' => $form->createView(),
                'supportedLanguages' => $supportedLanguages,
                'node' => $shot,
            ]
        );
    }

    public function reorder(
        Request $request,
        string $type,
        PageRepository $pageRepository,
        FileRepository $fileRepository
    ): JsonResponse {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $ids = $request->request->get('nodes');
        $ids = explode(',', $ids);

        switch ($type) {
            case 'file':
                $nodes = $fileRepository->findActiveByIds($ids, $this->getUser());
                break;
            case 'page':
            default:
                $nodes = $pageRepository->findActiveByIds($ids, $this->getUser());
                break;
        }

        /** @var Node $file */
        foreach ($nodes as $file) {
            $file->setOrder(array_search($file->getId(), $ids, false));
        }

        $this->entityManager->flush();

        return new JsonResponse('ok');
    }

    public function delete(Shot $shot, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($shot);
        $entityManager->flush();

         exit;
//        $this->denyAccessUnlessGranted('ROLE_USER');
//
//        switch ($type) {
//            case 'album':
//            default:
//                /** @var Album $node */
//                $node = $this->entityManager->getRepository(Album::class)->findOneBy([
//                    'user' => $this->getUser(),
//                    'id' => $id,
//                ]);
//                break;
//        }
//
//        if (null === $node) {
//            /** @noinspection PhpUnhandledExceptionInspection */
//            throw new Exception('Error. The node was not found');
//        }
//
//        $this->denyAccessUnlessGranted('modify', $node);
//
//        $node->setDeleted(true);
//        $this->entityManager->flush();

        return new JsonResponse('deleted');
    }
}
