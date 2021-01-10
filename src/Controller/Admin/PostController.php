<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Site;
use App\Form\Admin\PostType;
use App\Repository\FileRepository;

//use App\Repository\PageRepository;
use App\Repository\PageRepository;
use App\Repository\PostRepository;
use DateTime;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    private $entityManager;

    // todo: import repositories with auto-wiring
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->denyAccessUnlessGranted('edit', $post);

        $supportedLanguages = array_filter($param->get('supported_languages'),
            function ($language) use ($site) {
                return in_array($language, $site->getSupportedLanguages(), false);
            });
        $orderedFiles = UploadController::getOrderedFiles($post->getFiles()->toArray());


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

            $form->get('attachedFiles')->setData(UploadController::getOrderedFilesIdsConcatenated($orderedFiles));
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

            $attachedFiles = $request->request->get('post')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = explode(';', $attachedFiles);
                $postFiles = $fileRepository->getActiveByIds($attachedFilesIds, $this->getUser())->toArray();
                $post->setFiles($postFiles);

                if (!empty($postFiles) && null === $post->getDefaultImage()) {
                    $defaultImage = array_keys($postFiles)[0];
                    $post->setDefaultImage($postFiles[$defaultImage]);
                }
            }

            $post->setUpdatedAt(new DateTime());
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_admin_post_list', ['site' => $post->getSite()->getId()]);
        }

        return $this->render(
            'Admin/Post/post_edit.html.twig',
            [
                'form' => $form->createView(),
                'files' => $orderedFiles,
                'post' => $post,
                'supportedLanguages' => $supportedLanguages,
                'site' => $post->getSite(),
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

        $post = new Post();
        $post->setCreatedAt(new DateTime());
        $supportedLanguages = array_filter($param->get('supported_languages'),
            function ($language) use ($site) {
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
            $post->setUserCustomer($this->getUser());
            $post->setUpdatedAt(new DateTime());
            $post->setCreatedAt(new DateTime());

            $attachedFiles = $request->request->get('post')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = explode(';', $attachedFiles);
                $postFiles = $fileRepository->getActiveByIds($attachedFilesIds, $this->getUser())->toArray();
                $post->setFiles($postFiles);

                if (!empty($postFiles) && null === $post->getDefaultImage()) {
                    $defaultImage = array_keys($postFiles)[0];
                    $post->setDefaultImage($postFiles[$defaultImage]);
                }
            }

            $this->entityManager->persist($post);
            $this->entityManager->flush();

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

    public function list(
        Request $request,
        Site $site,
        PostRepository $postRepository,
        PageRepository $pageRepository
    ) {
        $posts = $postRepository->findAllByUserSite($this->getUser(), $site);

        return $this->render(
            'Admin/Post/post_list.html.twig',
            [
                'posts' => $posts,
                'site' => $site,
            ]
        );
    }

    public function delete(Post $post)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->denyAccessUnlessGranted('edit', $post);

        $post->setDeleted(true);
        $this->entityManager->flush();

        return new JsonResponse('deleted');
    }
}
