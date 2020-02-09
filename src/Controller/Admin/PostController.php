<?php

namespace App\Controller\Admin;

use App\Document\File;
use App\Document\Post;
use App\Form\Admin\PostType;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use App\Repository\PostRepository;
use Doctrine\ODM\MongoDB\MongoDBException;
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
     * @param Site $site
     * @param Post $post
     * @param FileRepository $fileRepository
     * @param ParameterBagInterface $param
     * @return Response
     * @throws MongoDBException
     */
    public function edit(
        Request $request,
        Site $site,
        Post $post,
        FileRepository $fileRepository,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('edit', $post);

        $supportedLanguages = array_filter($param->get('supported_languages'), function($language) use ($site) {
            return in_array($language, $site->getSupportedLanguages(), false);
        });

        $form = $this->createForm(PostType::class, $post, ['supported_languages' => $supportedLanguages]);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $currentTranslatedTitles = $post->getTranslatedTitle();
            $currentTranslatedContent = $post->getTranslatedContent();
            $currentTranslatedKeywords = $post->getTranslatedKeywords();
            $currentTranslatedMetaDescription = $post->getTranslatedMetaDescription();
            $currentUpdatedTranslatedExcerpt = $post->getTranslatedExcerpt();
            foreach ($supportedLanguages as $language) {
                $translatedTitle = isset($currentTranslatedTitles[$language]) ? $currentTranslatedTitles[$language] : '';
                $translatedContent = isset($currentTranslatedContent[$language]) ? $currentTranslatedContent[$language] : '';
                $translatedKeywords = isset($currentTranslatedKeywords[$language]) ? $currentTranslatedKeywords[$language] : '';
                $translatedMetaDescription = isset($currentTranslatedMetaDescription[$language]) ? $currentTranslatedMetaDescription[$language] : '';
                $translatedExcerpt = isset($currentUpdatedTranslatedExcerpt[$language]) ? $currentUpdatedTranslatedExcerpt[$language] : '';
                $form->get('title_' . $language)->setData($translatedTitle);
                $form->get('content_' . $language)->setData($translatedContent);
                $form->get('excerpt_' . $language)->setData($translatedExcerpt);
                $form->get('keywords_' . $language)->setData($translatedKeywords);
                $form->get('meta_description_' . $language)->setData($translatedMetaDescription);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $updatedTranslatedTitle = [];
            $updatedTranslatedContent = [];
            $updatedTranslatedExcerpt = [];
            $updatedTranslatedKeywords = [];
            $updatedTranslatedMetaDescription = [];
            foreach ($supportedLanguages as $language) {
                $updatedTranslatedTitle[$language] = $form['title_' . $language]->getData();
                $updatedTranslatedContent[$language] = $form['content_' . $language]->getData();
                $updatedTranslatedExcerpt[$language] = $form['excerpt_' . $language]->getData();
                $updatedTranslatedKeywords[$language] = $form['keywords_' . $language]->getData();
                $updatedTranslatedMetaDescription[$language] = $form['meta_description_' . $language]->getData();
            }

            $post->setTranslatedTitle($updatedTranslatedTitle);
            $post->setTranslatedContent($updatedTranslatedContent);
            $post->setTranslatedExcerpt($updatedTranslatedExcerpt);
            $post->setTranslatedKeywords($updatedTranslatedKeywords);
            $post->setTranslatedMetaDescription($updatedTranslatedMetaDescription);

            $this->documentManager->persist($post);


            $postFiles = [];
            $attachedFiles = $request->request->get('post')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = explode(';', $attachedFiles);
                $postFiles= $fileRepository->getActive($attachedFilesIds, $this->getUser())->toArray();
            }
            if(!empty($postFiles)) {
                $fileOrder = 0;
                foreach ($postFiles as $file) {
                    $file->setOrder($fileOrder);
                    $fileOrder++;
                }
            }
            $post->setFiles($postFiles);

            $this->documentManager->flush();

            return $this->redirectToRoute('user_admin_post_list', ['site' => $post->getSite()->getId()]);
        }

        $fileConcatenated = '';
        /** @var File $file */
        foreach ($post->getFiles() as $file) {
            $fileConcatenated .= $file->getId().';';
        }
        // todo: remove this thing
        $form->get('attachedFiles')->setData($fileConcatenated);


        $orderedFiles = [];
        /** @var File $file */
        foreach ($post->getFiles() as $file) {
            $orderedFiles[$file->getOrder()] = $file;
        }
        ksort($orderedFiles);

        return $this->render(
            'Admin/Post/post_edit.html.twig',
            [
                'form' => $form->createView(),
                'files' => $orderedFiles,
                'post' => $post,
                'supportedLanguages' => $supportedLanguages,
                'site' => $post->getSite()
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

        $this->denyAccessUnlessGranted('edit', $site);

        $post = new Post();
        $supportedLanguages = array_filter($param->get('supported_languages'), function($language) use ($site) {
            return in_array($language, $site->getSupportedLanguages(), false);
        });

        $form = $this->createForm(PostType::class, $post, ['supported_languages' => $supportedLanguages]);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $currentTranslatedTitles = $post->getTranslatedTitle();
            $currentTranslatedContent = $post->getTranslatedContent();
            $currentTranslatedExcerpt = $post->getTranslatedExcerpt();
            $currentTranslatedKeywords = $post->getTranslatedKeywords();
            $currentTranslatedMetaDescription = $post->getTranslatedMetaDescription();
            foreach ($supportedLanguages as $language) {
                $translatedTitle = isset($currentTranslatedTitles[$language]) ? $currentTranslatedTitles[$language] : '';
                $translatedContent = isset($currentTranslatedContent[$language]) ? $currentTranslatedContent[$language] : '';
                $currentTranslatedExcerpt = isset($currentTranslatedExcerpt[$language]) ? $currentTranslatedExcerpt[$language] : '';
                $translatedKeywords = isset($currentTranslatedKeywords[$language]) ? $currentTranslatedKeywords[$language] : '';
                $translatedMetaDescription = isset($currentTranslatedMetaDescription[$language]) ? $currentTranslatedMetaDescription[$language] : '';
                $form->get('title_' . $language)->setData($translatedTitle);
                $form->get('content_' . $language)->setData($translatedContent);
                $form->get('excerpt_' . $language)->setData($currentTranslatedExcerpt);
                $form->get('keywords_' . $language)->setData($translatedKeywords);
                $form->get('meta_description_' . $language)->setData($translatedMetaDescription);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $updatedTranslatedTitle = [];
            $updatedTranslatedContent = [];
            $updatedTranslatedExcerpt = [];
            $updatedTranslatedKeywords = [];
            $updatedTranslatedMetaDescription = [];
            foreach ($supportedLanguages as $language) {
                $updatedTranslatedTitle[$language] = $form['title_' . $language]->getData();
                $updatedTranslatedContent[$language] = $form['content_' . $language]->getData();
                $updatedTranslatedExcerpt[$language] = $form['excerpt_' . $language]->getData();
                $updatedTranslatedKeywords[$language] = $form['keywords_' . $language]->getData();
                $updatedTranslatedMetaDescription[$language] = $form['meta_description_' . $language]->getData();
            }

            $post->setTranslatedTitle($updatedTranslatedTitle);
            $post->setTranslatedContent($updatedTranslatedContent);
            $post->setTranslatedExcerpt($updatedTranslatedExcerpt);
            $post->setTranslatedKeywords($updatedTranslatedKeywords);
            $post->setTranslatedMetaDescription($updatedTranslatedMetaDescription);
            $post->setSite($site);
            $post->setUser($this->getUser());

            $this->documentManager->persist($post);

            // save files
            $postFiles = [];
            $attachedFiles = $request->request->get('post')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = explode(';', $attachedFiles);
                $postFiles= $fileRepository->getActive($attachedFilesIds, $this->getUser())->toArray();
            }
            if(!empty($postFiles)) {
                $fileOrder = 0;
                foreach ($postFiles as $file) {
                    $file->setOrder($fileOrder);
                    $fileOrder++;
                }
            }
            $post->setFiles($postFiles);
            // end save files

            $this->documentManager->flush();

            return $this->redirectToRoute('user_admin_post_list', ['site' => $post->getSite()->getId()]);
        }

        return $this->render(
            'Admin/Post/post_edit.html.twig',
            [
                'form' => $form->createView(),
                'supportedLanguages' => $supportedLanguages,
                'post' => $post,
                'site' => $site,
            ]
        );
    }

    public function list(Request $request, Site $site, PostRepository $postRepository, PageRepository $pageRepository)
    {
        $posts = $postRepository->findBy(['site' => $site]);
        $pages = $pageRepository->findBy(['site' => $site], ['order' => 'DESC ']);

        return $this->render(
            'Admin/Post/post_list.html.twig',
            [
                'posts' => $posts,
                'pages' => $pages,
                'site' => $site,
            ]
        );
    }
}
