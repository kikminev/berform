<?php


namespace App\Service\Site;


use App\Entity\Post;
use App\Entity\Site;
use App\Repository\PostRepository;

class TemplatePiecesProvider
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    const TEMPLATE_EXPLORER = 'explorer';

    public function getPieces(Site $site): array
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
        } else {
            $posts = $this->postRepository->findActivePostsBySite($site);

            return [
                'posts' => $posts
            ];
        }
    }
}
