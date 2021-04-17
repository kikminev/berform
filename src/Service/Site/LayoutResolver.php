<?php

namespace App\Service\Site;


use App\Entity\Site;

class LayoutResolver
{
    public const SITE_CATEGORY_PHOTOGRAPHY = 'photography',
        SITE_CATEGORY_BLOG = 'blog';

    public function getSiteTemplateCss(Site $site): string
    {
        switch ($site->getCategory()) {
            case static::SITE_CATEGORY_PHOTOGRAPHY:
                $nameSpace = 'photography_site';
                break;
            case static::SITE_CATEGORY_BLOG:
                $nameSpace = 'blog_site';
                break;
            default:
                $nameSpace = 'standard_site';
        }

        return $nameSpace . '/' . $site->getTemplate() . '_template.css';
    }

    public function getPageTemplate(Site $site, string $slug): string
    {
        switch ($slug) {
            case 'home':
                $page = 'home_page.html.twig';
                break;
            case 'photography':
                $page = 'photography.html.twig';
                break;
            case 'contact':
            case 'contact-me':
            case 'contact-us':
                $page = 'contact_page.html.twig';
                break;
            default:
                $page = 'page.html.twig';
        }


        switch ($site->getCategory()) {
            case static::SITE_CATEGORY_BLOG:
                return 'UserSite/BlogSite/' . $site->getTemplate() . '/' . $page;
            case static::SITE_CATEGORY_PHOTOGRAPHY:
                return 'UserSite/PhotographySite/' . $site->getTemplate() . '/' . $page;
            default:
                return 'UserSite/StandardSite/' . $page;
        }
    }

    public function getLayout(Site $site): string
    {
        switch ($site->getCategory()) {
            case static::SITE_CATEGORY_PHOTOGRAPHY:
                return 'PhotographySite\\' . $site->getTemplate() . '\layout';
            case static::SITE_CATEGORY_BLOG:
                return 'BlogSite\\' . $site->getTemplate() . '\layout';
            default:
                return 'StandardSite/layout';
        }
    }

    public function getBlogList(Site $site): string
    {
        switch ($site->getCategory()) {
            case static::SITE_CATEGORY_PHOTOGRAPHY:
            default:
                return 'UserSite/PhotographySite\\' . $site->getTemplate() . '/list.html.twig';
            case static::SITE_CATEGORY_BLOG:
                return 'UserSite/BlogSite\\' . $site->getTemplate() . '/list.html.twig';
        }
    }

    public function getBlogPostTemplate(Site $site): string
    {
        switch ($site->getCategory()) {
            case static::SITE_CATEGORY_PHOTOGRAPHY:
            default:
                return 'UserSite/PhotographySite\\' . $site->getTemplate() . '/post.html.twig';
            case static::SITE_CATEGORY_BLOG:
                return 'UserSite/BlogSite\\' . $site->getTemplate() . '/post.html.twig';
        }
    }
}
