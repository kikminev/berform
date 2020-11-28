<?php

namespace App\Controller\Admin;

use App\Document\Album;
use App\Document\Shot;
use App\Form\Admin\NodeType;
use App\Form\Admin\ShotType;
use App\Repository\AlbumRepository;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use function Clue\StreamFilter\append;
use Exception;
use MongoDB\BSON\ObjectId;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use App\Document\Page;
use App\Document\Node;
use App\Document\File;
use App\Form\Admin\PageType;

class NodeController extends AbstractController
{
    private $documentManager;
    private $albumRepository;

    // todo: import repositories with auto-wiring
    public function __construct(DocumentManager $documentManager, AlbumRepository $albumRepository)
    {
        $this->documentManager = $documentManager;
        $this->albumRepository = $albumRepository;
    }

    /**
     * @param Request $request
     * @param Page $page
     * @param FileRepository $fileRepository
     * @param ParameterBagInterface $param
     * @return Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     * @throws Exception
     */
    public function list(
        Request $request,
        String $type,
        Site $site,
        FileRepository $fileRepository,
        DocumentManager $documentManager,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        switch ($type) {
            case 'shot':
                $qb = $documentManager->createQueryBuilder(Shot::class);
                break;
            case 'album':
            default:
                $qb = $documentManager->createQueryBuilder(Album::class);
                break;
        }

        $qb->addAnd($qb->expr()->field('user')->equals($this->getUser()));
        $qb->addAnd($qb->expr()->field('site')->equals($site->getId()));
        $qb->addAnd($qb->expr()->field('deleted')->notEqual(true));
        $nodes = $qb->getQuery()->execute();

        return $this->render(
            'Admin/Node/node_list.html.twig',
            [
                'type' => $type,
                'nodes' => $nodes,
                'site' => $site,
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $type
     * @param string $id
     * @param FileRepository $fileRepository
     * @param DocumentManager $documentManager
     * @param ParameterBagInterface $param
     * @return Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function edit(
        Request $request,
        string $type,
        string $id,
        FileRepository $fileRepository,
        DocumentManager $documentManager,
        ParameterBagInterface $param
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        switch ($type) {
            case 'album':
            default:
                /** @var Album $node */
                $node = $documentManager->getRepository(Album::class)->findOneBy([
                    'user' => $this->getUser(),
                    'id' => $id,
                ]);
                break;
        }

        if (null === $node) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new Exception('Error');
        }

        $this->denyAccessUnlessGranted('modify', $node);

        /** @var Node $node */
        $site = $node->getSite();
        $supportedLanguages = array_filter($param->get('supported_languages'), function ($language) use ($site) {
            return in_array($language, $site->getSupportedLanguages(), false);
        });

        $form = $this->createForm(NodeType::class, $node, ['supported_languages' => $supportedLanguages]);
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

            $node->setTranslatedTitle($updatedTranslatedTitle);
            $node->setTranslatedContent($updatedTranslatedContent);
            $node->setTranslatedKeywords($updatedTranslatedKeywords);
            $node->setTranslatedMetaDescription($updatedTranslatedMetaDescription);

            $attachedFiles = $request->request->get('node')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = explode(';', $attachedFiles);
                $nodeFiles = $fileRepository->getActiveByIds($attachedFilesIds, $this->getUser())->toArray();
                $node->setFiles($nodeFiles);

                if(!empty($nodeFiles) && null === $node->getDefaultImage()) {
                    $defaultImage = array_keys($nodeFiles)[0];
                    $node->setDefaultImage($nodeFiles[$defaultImage]);
                }
            }

            $this->documentManager->persist($node);
            $this->documentManager->flush();

            return $this->redirectToRoute('user_admin_node_list', [
                'site' => $node->getSite()->getId(),
                'type' => 'album',
            ]);
        }

        // todo: this needs to be refactored - SHOW ORDERED FILES
        $orderedFiles = UploadController::getOrderedFiles($node->getFiles()->toArray());
        $form->get('attachedFiles')->setData(UploadController::getOrderedFilesIdsConcatenated($orderedFiles));

        return $this->render(
            'Admin/Node/node_edit.html.twig',
            [
                'form' => $form->createView(),
                'files' => $orderedFiles,
                'page' => $node,
                'supportedLanguages' => $supportedLanguages,
                'site' => $node->getSite(),
                'node' => $node,
            ]
        );
    }

    /**
     * @param Request $request
     * @param Site $site
     * @param string $type
     * @param FileRepository $fileRepository
     * @param ParameterBagInterface $param
     * @return Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function create(
        Request $request,
        Site $site,
        string $type,
        FileRepository $fileRepository,
        ParameterBagInterface $param
    ): Response {
        $this->denyAccessUnlessGranted('modify', $site);
        $supportedLanguages = array_filter($param->get('supported_languages'), function ($language) use ($site) {
            return in_array($language, $site->getSupportedLanguages(), false);
        });

        switch ($type) {
            case 'shot':
                $node = new Shot();
                $form = $this->createForm(ShotType::class, $node, ['supported_languages' => $supportedLanguages]);
                $editTemplate = 'Admin/Node/single_image_edit.html.twig';
                break;
            case 'album':
                $node = new Album();
                $form = $this->createForm(NodeType::class, $node, ['supported_languages' => $supportedLanguages]);
                $editTemplate = 'Admin/Node/node_edit.html.twig';
                break;
        }

        $node->setType($type);
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
            $node->setUser($this->getUser());

            $attachedFiles = $request->request->get('node')['attachedFiles'] ?? false;
            if('shot' === $type) {
                $attachedFiles = $request->request->get('shot')['attachedFiles'] ?? false;
            }

            if ($attachedFiles) {
                $attachedFilesIds = explode(';', $attachedFiles);
                $nodeFiles = $fileRepository->getActiveByIds($attachedFilesIds, $this->getUser())->toArray();
                $node->setFiles($nodeFiles);

                if(!empty($nodeFiles) && null === $node->getDefaultImage()) {
                    $defaultImage = array_keys($nodeFiles)[0];
                    $node->setDefaultImage($nodeFiles[$defaultImage]);
                }
            }

            $this->documentManager->persist($node);
            $this->documentManager->flush();

            return $this->redirectToRoute('user_admin_node_list', [
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

    /**
     * @param Request $request
     * @param string $type
     * @param PageRepository $pageRepository
     * @param FileRepository $fileRepository
     * @return JsonResponse
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
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
                $nodes = $fileRepository->getActiveByIds($ids, $this->getUser());
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

        $this->documentManager->flush();

        return new JsonResponse('ok');
    }

    public function delete(DocumentManager $documentManager, string $type, string $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        switch ($type) {
            case 'album':
            default:
                /** @var Album $node */
                $node = $documentManager->getRepository(Album::class)->findOneBy([
                    'user' => $this->getUser(),
                    'id' => $id,
                ]);
                break;
        }

        if (null === $node) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new Exception('Error. The node was not found');
        }

        $this->denyAccessUnlessGranted('modify', $node);

        $node->setDeleted(true);
        $documentManager->flush();

        return new JsonResponse('deleted');
    }
}
