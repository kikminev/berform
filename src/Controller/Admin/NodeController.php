<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Node;
use App\Entity\Page;
use App\Entity\Shot;
use App\Entity\Site;
use App\Form\Admin\AlbumType;
use App\Form\Admin\ShotType;
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

class NodeController extends AbstractController
{
    private $entityManager;
    private $albumRepository;

    // todo: import repositories with auto-wiring
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $ids = array_filter(explode(',', $ids));

        switch ($type) {
            case 'file':
                $nodes = $fileRepository->getActiveByIds($ids, $this->getUser());
                break;
            case 'page':
            default:
            $nodes = $pageRepository->findActiveByIdsAndUser($ids, $this->getUser());
                break;
        }

        echo count($nodes);
        /** @var Node $file */
        foreach ($nodes as $file) {
            $file->setOrder(array_search($file->getId(), $ids, false));
        }

        $this->entityManager->flush();

        return new JsonResponse('ok');
    }

    public function delete(string $type, string $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        switch ($type) {
            case 'album':
            default:
                /** @var Album $node */
                $node = $this->entityManager->getRepository(Album::class)->findOneBy([
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
        $this->entityManager->flush();

        return new JsonResponse('deleted');
    }
}
