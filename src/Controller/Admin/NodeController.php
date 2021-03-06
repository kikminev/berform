<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Node;

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

    public function reorder(
        Request $request,
        string $type,
        PageRepository $pageRepository,
        ShotRepository $shotRepository,
        FileRepository $fileRepository
    ): JsonResponse {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $ids = $request->request->get('nodes');
        $ids = array_filter(explode(',', $ids));

        switch ($type) {
            case 'file':
                $nodes = $fileRepository->findActiveByIds($ids, $this->getUser());
                break;
            case 'shot':
                $nodes = $shotRepository->findActiveByIdsAndUser($ids, $this->getUser());
                break;
            case 'page':
            default:
            $nodes = $pageRepository->findActiveByIdsAndUser($ids, $this->getUser());
                break;
        }

        foreach ($nodes as $file) {
            $file->setSequenceOrder(array_search($file->getId(), $ids, false));
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
