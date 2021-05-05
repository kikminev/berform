<?php

namespace App\Form\Admin;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SupportedLanguageToNumberTransformer implements DataTransformerInterface
{
    private $translator;
    private $param;

    public function __construct(ParameterBagInterface $param, TranslatorInterface $translator)
    {
        $this->param = $param;
        $this->translator = $translator;
    }

    public function transform($supportedLanguages)
    {
        // todo: show translated labels
        return array_flip($supportedLanguages);
    }

    public function reverseTransform($selectedLanguageKeys)
    {
        $allLanguages = $this->param->get('supported_languages');

        $selectedLanguages = [];
        foreach($selectedLanguageKeys as $key) {
            $selectedLanguages[$key] = $allLanguages[$key];
        }

        return $selectedLanguages;
    }
}
