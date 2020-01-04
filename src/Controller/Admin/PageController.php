<?php

namespace App\Controller\Admin;

use App\Repository\FileRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use App\Document\Page;
use App\Document\File;
use App\Form\Admin\PageType;

class PageController extends AbstractController
{
    private $documentManager;

    // todo: import repositories with auto-wiring
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @param Request $request
     * @param Page $page
     * @param FileRepository $fileRepository
     * @param ParameterBagInterface $param
     * @return Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function edit(
        Request $request,
        Page $page,
        FileRepository $fileRepository,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('edit', $page);

        $site = $page->getSite();
        $supportedLanguages = array_filter($param->get('supported_languages'), function($language) use ($site) {
            return in_array($language, $site->getSupportedLanguages(), false);
        });

        $form = $this->createForm(PageType::class, $page, ['supported_languages' => $supportedLanguages]);
        $form->handleRequest($request);



        if (!$form->isSubmitted()) {
            $currentTranslatedTitles = $page->getTranslatedTitle();
            $currentTranslatedContent = $page->getTranslatedContent();
            $currentTranslatedKeywords = $page->getTranslatedKeywords();
            $currentTranslatedMetaDescription = $page->getTranslatedMetaDescription();
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

            $page->setTranslatedTitle($updatedTranslatedTitle);
            $page->setTranslatedContent($updatedTranslatedContent);
            $page->setTranslatedKeywords($updatedTranslatedKeywords);
            $page->setTranslatedMetaDescription($updatedTranslatedMetaDescription);

            $this->documentManager->persist($page);

            $pageFiles = [];
            $attachedFiles = $request->request->get('page')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = explode(';', $attachedFiles);
                $pageFiles= $fileRepository->getActiveFiles($attachedFilesIds, $this->getUser())->toArray();
            }
            $page->setFiles($pageFiles);

            $this->documentManager->flush();

            return $this->redirectToRoute('user_admin_site_build', ['id' => $page->getSite()->getId()]);
        } else {
            $fileConcatenated = '';
            foreach ($page->getFiles() as $file) {
                $fileConcatenated .= $file->getId().';';
            }
            // todo: remove this thing
            $form->get('attachedFiles')->setData($fileConcatenated);
        }

        return $this->render(
            'Admin/Page/page_edit.html.twig',
            [
                'form' => $form->createView(),
                'files' => $page->getFiles(),
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
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function create(
        Request $request,
        Site $site,
        FileRepository $fileRepository,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('edit', $site);

        $page = new Page();
        $site = $page->getSite();
        $supportedLanguages = array_filter($param->get('supported_languages'), function($language) use ($site) {
            return in_array($language, $site->getSupportedLanguages(), false);
        });

        $form = $this->createForm(PageType::class, $page, ['supported_languages' => $supportedLanguages]);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $currentTranslatedTitles = $page->getTranslatedTitle();
            $currentTranslatedContent = $page->getTranslatedContent();
            $currentTranslatedKeywords = $page->getTranslatedKeywords();
            $currentTranslatedMetaDescription = $page->getTranslatedMetaDescription();
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

            $page->setTranslatedTitle($updatedTranslatedTitle);
            $page->setTranslatedContent($updatedTranslatedContent);
            $page->setTranslatedKeywords($updatedTranslatedKeywords);
            $page->setTranslatedMetaDescription($updatedTranslatedMetaDescription);
            $page->setSite($site);
            $page->setUser($this->getUser());

            $this->documentManager->persist($page);

            $pageFiles = [];
            $attachedFiles = $request->request->get('page')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = explode(';', $attachedFiles);
                $pageFiles= $fileRepository->getActiveFiles($attachedFilesIds, $this->getUser())->toArray();
            }
            $page->setFiles($pageFiles);

            $this->documentManager->flush();

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
}
