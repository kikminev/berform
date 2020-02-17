<?php

namespace App\Service\Site;

use App\Document\Site;

class LayoutResolver
{
    public const SITE_CATEGORY_PHOTOGRAPHY = 'photography',
        SITE_CATEGORY_BLOG = 'blog',
        SITE_CATEGORY_STANDARD = 'standard';

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

        switch ($site->getCategory()) {
            case static::SITE_CATEGORY_BLOG:
                return ($slug === 'home') ? 'UserSite/BlogSite/home_page.html.twig' : 'UserSite/BlogSite/page.html.twig';
            case static::SITE_CATEGORY_PHOTOGRAPHY:
                return ($slug === 'home') ? 'UserSite/PhotographySite/minimal/home_page.html.twig' : 'UserSite/PhotographySite/minimal/page.html.twig';
            default:
                return 'UserSite/StandardSite/page.html.twig';
        }
    }

    public function getLayout(Site $site): string
    {

        switch ($site->getCategory()) {
            case static::SITE_CATEGORY_PHOTOGRAPHY:
                return 'PhotographySite\minimal\layout';
            case static::SITE_CATEGORY_BLOG:
                return 'BlogSite\layout';
            default:
                return 'StandardSite/layout';
        }
    }
}
