<?php

namespace App\Service\Site;

class LayoutResolver
{
    private const BLOG_TEMPLATES = [
        'blog_minimal'
    ];

    public function isBlogTemplate(string $template): bool
    {
        return in_array($template, static::BLOG_TEMPLATES, true);
    }

    public function getLayout(string $template): string
    {
        return $this->isBlogTemplate($template) ? 'blog_layout' : 'layout';
    }
}
