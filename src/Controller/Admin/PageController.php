<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use DateTime;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Document\Site;
use App\Form\Admin\PageType;

class PageController extends AbstractController
{
    private $entityManager;

    // todo: import repositories with auto-wiring
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @param Page $page
     * @param FileRepository $fileRepository
     * @param ParameterBagInterface $param
     * @return Response
     * @throws MongoDBException
     */
    public function edit(
        Request $request,
        Page $page,
        FileRepository $fileRepository,
        PageRepository $pageRepository,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->denyAccessUnlessGranted('edit', $page);

        $site = $page->getSite();
        $supportedLanguages = array_filter($param->get('supported_languages'),
            function ($language) use ($site) {
                return in_array($language, $site->getSupportedLanguages(), false);
            });

        $orderedFiles = UploadController::getOrderedFiles($page->getFiles()->toArray());

        $form = $this->createForm(PageType::class, $page, ['supported_languages' => $supportedLanguages]);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $currentTranslatedTitles = $page->getTranslatedTitle();
            $currentTranslatedMenuLink = $page->getTranslatedMenuLink();
            $currentTranslatedContent = $page->getTranslatedContent();
            $currentTranslatedKeywords = $page->getTranslatedKeywords();
            $currentTranslatedMetaDescription = $page->getTranslatedMetaDescription();
            foreach ($supportedLanguages as $language) {
                $translatedTitle = isset($currentTranslatedTitles[$language]) ? $currentTranslatedTitles[$language] : '';
                $translatedMenuLink = isset($currentTranslatedMenuLink[$language]) ? $currentTranslatedMenuLink[$language] : '';
                $translatedContent = isset($currentTranslatedContent[$language]) ? $currentTranslatedContent[$language] : '';
                $translatedKeywords = isset($currentTranslatedKeywords[$language]) ? $currentTranslatedKeywords[$language] : '';
                $translatedMetaDescription = isset($currentTranslatedMetaDescription[$language]) ? $currentTranslatedMetaDescription[$language] : '';
                $form->get('title_' . $language)->setData($translatedTitle);
                $form->get('menu_link_' . $language)->setData($translatedMenuLink);
                $form->get('content_' . $language)->setData($translatedContent);
                $form->get('keywords_' . $language)->setData($translatedKeywords);
                $form->get('meta_description_' . $language)->setData($translatedMetaDescription);
            }
            $form->get('attachedFiles')->setData(UploadController::getOrderedFilesIdsConcatenated($orderedFiles));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $updatedTranslatedTitle = [];
            $updatedTranslatedMenuLink = [];
            $updatedTranslatedContent = [];
            $updatedTranslatedKeywords = [];
            $updatedTranslatedMetaDescription = [];
            foreach ($supportedLanguages as $language) {
                $updatedTranslatedTitle[$language] = $form['title_' . $language]->getData();
                $updatedTranslatedMenuLink[$language] = $form['menu_link_' . $language]->getData();
                $updatedTranslatedContent[$language] = $form['content_' . $language]->getData();
                $updatedTranslatedKeywords[$language] = $form['keywords_' . $language]->getData();
                $updatedTranslatedMetaDescription[$language] = $form['meta_description_' . $language]->getData();
            }

            $page->setTranslatedTitle($updatedTranslatedTitle);
            $page->setTranslatedMenuLink($updatedTranslatedMenuLink);
            $page->setTranslatedContent($updatedTranslatedContent);
            $page->setTranslatedKeywords($updatedTranslatedKeywords);
            $page->setTranslatedMetaDescription($updatedTranslatedMetaDescription);

            $attachedFiles = $request->request->get('page')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = array_filter(explode(';', $attachedFiles));

                $pageFiles = $fileRepository->getActiveByIds($attachedFilesIds, $this->getUser());
                foreach ($pageFiles as $attachedFile) {
                    $page->addFile($attachedFile);
                }

                if (!empty($pageFiles) && null === $page->getDefaultImage()) {
                    $defaultImage = array_keys($pageFiles)[0];
                    $page->setDefaultImage($pageFiles[$defaultImage]);
                }
            }

            $page->setUpdatedAt(new DateTime());
            $this->entityManager->persist($page);
            $this->entityManager->flush();


            return $this->redirectToRoute('user_admin_site_build', ['id' => $page->getSite()->getId()]);
        }

        return $this->render(
            'Admin/Page/page_edit.html.twig',
            [
                'form' => $form->createView(),
                'files' => $orderedFiles,
                'page' => $page,
                'supportedLanguages' => $supportedLanguages,
                'site' => $page->getSite(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param Site $site
     * @param FileRepository $fileRepository
     * @param ParameterBagInterface $param
     * @return Response
     * @throws MongoDBException
     */
    public function create(
        Request $request,
        Site $site,
        FileRepository $fileRepository,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->denyAccessUnlessGranted('modify', $site);

        $page = new Page();
        $page->setCreatedAt(new DateTime());
        $page->setSite($site);
        $page->setUser($this->getUser());

        $supportedLanguages = array_filter($param->get('supported_languages'),
            function ($language) use ($site) {
                return in_array($language, $site->getSupportedLanguages(), false);
            });

        $form = $this->createForm(PageType::class, $page, ['supported_languages' => $supportedLanguages]);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $currentTranslatedTitles = $page->getTranslatedTitle();
            $currentTranslatedMenuLink = $page->getTranslatedMenuLink();
            $currentTranslatedContent = $page->getTranslatedContent();
            $currentTranslatedKeywords = $page->getTranslatedKeywords();
            $currentTranslatedMetaDescription = $page->getTranslatedMetaDescription();
            foreach ($supportedLanguages as $language) {
                $translatedTitle = isset($currentTranslatedTitles[$language]) ? $currentTranslatedTitles[$language] : '';
                $translatedMenuLink = isset($currentTranslatedMenuLink[$language]) ? $currentTranslatedMenuLink[$language] : '';
                $translatedContent = isset($currentTranslatedContent[$language]) ? $currentTranslatedContent[$language] : '';
                $translatedKeywords = isset($currentTranslatedKeywords[$language]) ? $currentTranslatedKeywords[$language] : '';
                $translatedMetaDescription = isset($currentTranslatedMetaDescription[$language]) ? $currentTranslatedMetaDescription[$language] : '';
                $form->get('title_' . $language)->setData($translatedTitle);
                $form->get('menu_link_' . $language)->setData($translatedMenuLink);
                $form->get('content_' . $language)->setData($translatedContent);
                $form->get('keywords_' . $language)->setData($translatedKeywords);
                $form->get('meta_description_' . $language)->setData($translatedMetaDescription);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $updatedTranslatedTitle = [];
            $updatedTranslatedMenuLink = [];
            $updatedTranslatedContent = [];
            $updatedTranslatedKeywords = [];
            $updatedTranslatedMetaDescription = [];
            foreach ($supportedLanguages as $language) {
                $updatedTranslatedTitle[$language] = $form['title_' . $language]->getData();
                $updatedTranslatedMenuLink[$language] = $form['menu_link_' . $language]->getData();
                $updatedTranslatedContent[$language] = $form['content_' . $language]->getData();
                $updatedTranslatedKeywords[$language] = $form['keywords_' . $language]->getData();
                $updatedTranslatedMetaDescription[$language] = $form['meta_description_' . $language]->getData();
            }

            $page->setTranslatedTitle($updatedTranslatedTitle);
            $page->setTranslatedMenuLink($updatedTranslatedMenuLink);
            $page->setTranslatedContent($updatedTranslatedContent);
            $page->setTranslatedKeywords($updatedTranslatedKeywords);
            $page->setTranslatedMetaDescription($updatedTranslatedMetaDescription);

            $attachedFiles = $request->request->get('page')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = explode(';', $attachedFiles);
                $pageFiles = $fileRepository->getActiveByIds($attachedFilesIds, $this->getUser())->toArray();
                $page->setFiles($pageFiles);

                if (!empty($pageFiles) && null === $page->getDefaultImage()) {
                    $defaultImage = array_keys($pageFiles)[0];
                    $page->setDefaultImage($pageFiles[$defaultImage]);
                }
            }

            $this->entityManager->persist($page);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_admin_site_build', ['id' => $site->getId()]);
        }

        return $this->render(
            'Admin/Page/page_edit.html.twig',
            [
                'site' => $site,
                'form' => $form->createView(),
                'supportedLanguages' => $supportedLanguages,
                'page' => $page,
            ]
        );
    }

    public function delete(
        Page $page
    ) {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->denyAccessUnlessGranted('edit', $page);

        $page->setDeleted(true);
        $this->entityManager->flush();

        return new JsonResponse('deleted');
    }
}