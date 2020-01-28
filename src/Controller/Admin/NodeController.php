<?php

namespace App\Controller\Admin;

use App\Document\Album;
use App\Form\Admin\NodeType;
use App\Repository\AlbumRepository;
use App\Repository\FileRepository;
use Exception;
use MongoDB\BSON\ObjectId;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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

        switch ($type) {
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
                'nodes' => $nodes,
                'site' => $site,
            ]
        );
    }

    public function edit(
        Request $request,
        string $type,
        string $id,
        FileRepository $fileRepository,
        DocumentManager $documentManager,
        ParameterBagInterface $param
    ): Response {
        switch ($type) {
            case 'album':
            default:
                /** @var Album $node */
                $node = $documentManager->getRepository(Album::class)->findOneBy(['user' => $this->getUser(), 'id' => $id]);
                break;
        }

        if(null === $node) {
            throw new Exception('Error');
        }

        //$this->denyAccessUnlessGranted('edit', $node);

        /** @var Node $node */
        $site = $node->getSite();
        $supportedLanguages = array_filter($param->get('supported_languages'), function($language) use ($site) {
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

            $this->documentManager->persist($node);

            $nodeFiles = [];
            $attachedFiles = $request->request->get('node')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = explode(';', $attachedFiles);
                $nodeFiles= $fileRepository->getActiveFiles($attachedFilesIds, $this->getUser())->toArray();
            }
            $node->setFiles($nodeFiles);

            $this->documentManager->flush();

            return $this->redirectToRoute('user_admin_node_list', ['site' => $node->getSite()->getId(), 'type' => 'album']);

        }

        $fileConcatenated = '';
        foreach ($node->getFiles() as $file) {
            $fileConcatenated .= $file->getId().';';
        }
        // todo: remove this thing
        $form->get('attachedFiles')->setData($fileConcatenated);

        return $this->render(
            'Admin/Node/node_edit.html.twig',
            [
                'form' => $form->createView(),
                'files' => $node->getFiles(),
                'page' => $node,
                'supportedLanguages' => $supportedLanguages,
                'site' => $node->getSite(),
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

        $this->denyAccessUnlessGranted('edit', $site);

        switch ($type) {
            case 'album':
                $node = new Album();
                break;
        }

        $node->setType($type);
        $supportedLanguages = array_filter($param->get('supported_languages'), function($language) use ($site) {
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
            $node->setSite($site);
            $node->setUser($this->getUser());

            $this->documentManager->persist($node);

            $nodeFiles = [];
            $attachedFiles = $request->request->get('node')['attachedFiles'] ?? false;
            if ($attachedFiles) {
                $attachedFilesIds = explode(';', $attachedFiles);
                $nodeFiles= $fileRepository->getActiveFiles($attachedFilesIds, $this->getUser())->toArray();
            }
            $node->setFiles($nodeFiles);

            $this->documentManager->flush();

            return $this->redirectToRoute('user_admin_site_build', ['id' => $site->getId()]);
        }

        return $this->render(
            'Admin/Node/node_edit.html.twig',
            [
                'site' => $site,
                'form' => $form->createView(),
                'supportedLanguages' => $supportedLanguages,
                'node' => $node,
            ]
        );
    }
}
