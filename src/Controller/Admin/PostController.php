<?php

namespace App\Controller\Admin;

use App\Document\Post;
use App\Form\Admin\PostType;
use App\Repository\FileRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;

class PostController extends AbstractController
{
    private $documentManager;

    // todo: import repositories with auto-wiring
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @param Request $request
     * @param Post $post
     * @param FileRepository $fileRepository
     * @param ParameterBagInterface $param
     * @return Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function edit(
        Request $request,
        Post $post,
        FileRepository $fileRepository,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('edit', $post);

        $supportedLanguages = $param->get('supported_languages');
        $form = $this->createForm(PostType::class, $post, ['supported_languages' => $supportedLanguages]);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $currentTranslatedTitles = $post->getTranslatedTitle();
            $currentTranslatedContent = $post->getTranslatedContent();
            $currentTranslatedKeywords = $post->getTranslatedKeywords();
            $currentTranslatedMetaDescription = $post->getTranslatedMetaDescription();
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

            $post->setTranslatedTitle($updatedTranslatedTitle);
            $post->setTranslatedContent($updatedTranslatedContent);
            $post->setTranslatedKeywords($updatedTranslatedKeywords);
            $post->setTranslatedMetaDescription($updatedTranslatedMetaDescription);

            $this->documentManager->persist($post);
            $this->documentManager->flush();

            return $this->redirectToRoute('user_admin_site_build', ['id' => $post->getSite()->getId()]);
        }

        $files = $fileRepository->getPostFiles($post);

        return $this->render(
            'Admin/post_edit.html.twig',
            [
                'form' => $form->createView(),
                'files' => $files,
                'post' => $post,
            ]
        );
    }

    /**
     * @param Request $request
     * @param Site $site
     * @param ParameterBagInterface $param
     * @return Response
     */
    public function create(
        Request $request,
        Site $site,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('edit', $site);

        $post = new Post();
        $supportedLanguages = $param->get('supported_languages');
        $form = $this->createForm(PostType::class, $post, ['supported_languages' => $supportedLanguages]);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $currentTranslatedTitles = $post->getTranslatedTitle();
            $currentTranslatedContent = $post->getTranslatedContent();
            $currentTranslatedKeywords = $post->getTranslatedKeywords();
            $currentTranslatedMetaDescription = $post->getTranslatedMetaDescription();
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

            $post->setTranslatedTitle($updatedTranslatedTitle);
            $post->setTranslatedContent($updatedTranslatedContent);
            $post->setTranslatedKeywords($updatedTranslatedKeywords);
            $post->setTranslatedMetaDescription($updatedTranslatedMetaDescription);
            $post->setSite($site);
            $post->setUser($this->getUser());

            $this->documentManager->persist($post);
            $this->documentManager->flush();

            return $this->redirectToRoute('user_admin_site_build', ['id' => $site->getId()]);
        }

        return $this->render(
            'Admin/post_edit.html.twig',
            [
                'form' => $form->createView(),
                'post' => $post,
            ]
        );
    }
}
