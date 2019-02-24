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
use Symfony\Component\Routing\Annotation\Route;

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
     * @return Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function edit(
        Request $request,
        Page $page,
        FileRepository $fileRepository,
        ParameterBagInterface $param
    ): Response {
        $supportedLanguages = $param->get('supported_languages');
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
            $this->documentManager->flush();
        }

        $files = $fileRepository->getPageFiles($page);

        return $this->render(
            'Admin/page_edit.html.twig',
            [
                'form' => $form->createView(),
                'files' => $files,
                'page' => $page,
            ]
        );
    }
}
