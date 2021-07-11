<?php


namespace App\Service\Site;


use App\Entity\Post;
use App\Entity\Site;
use App\Repository\PostRepository;
use App\Repository\ShotRepository;

class TemplatePiecesProvider
{
    private PostRepository $postRepository;
    private ShotRepository $shotRepository;

    public function __construct(PostRepository $postRepository, ShotRepository $shotRepository)
    {
        $this->postRepository = $postRepository;
        $this->shotRepository = $shotRepository;
    }

    const TEMPLATE_EXPLORER = 'explorer';
    const TEMPLATE_PHOTOGRAPHY = 'photography';

    public function getPieces(Site $site, string $pageSlug): array
    {
        if (self::TEMPLATE_EXPLORER === $site->getTemplateSystemCode()) {
            $posts = $this->postRepository->findActivePostsBySite($site);
            $featuredParallaxPost = null;
            $featuredPost = $posts[0];
            $filteredPosts = [];
            foreach ($posts as $post) {
                /** @var Post $post */
                if (null === $featuredParallaxPost && true === $post->getFeaturedParallax()) {
                    $featuredParallaxPost = $post;
                } else {
                    $filteredPosts[] = $post;
                }
            }
            array_shift($filteredPosts);

            return [
                'posts' => $filteredPosts,
                'featuredPost' => $featuredPost,
                'featuredPostInParallax' => $featuredParallaxPost,
            ];
        }

        if (self::TEMPLATE_PHOTOGRAPHY === $site->getTemplateSystemCode() && $pageSlug === 'home') {

            $shots = $this->shotRepository->getActiveBySite($site);
            return [
                'shots' => $shots,
            ];
        }

        return [];
    }
}
