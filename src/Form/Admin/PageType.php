<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Document\Page;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Contracts\Translation\TranslatorInterface;


class PageType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['attr' => ['class' => 'slug_source'], 'label' => $this->translator->trans('form_page_name')]);
        $builder->add('slug', TextType::class, ['attr' => ['class' => 'slug_input', 'readonly' => true], 'label' => $this->translator->trans('form_page_slug')]);

        foreach ($options['supported_languages'] as $language) {
            $builder->add('title_'.$language, TextType::class, ['mapped' => false, 'required' => false, 'label' => $this->translator->trans('form_page_title')]);
            $builder->add('content_'.$language, TextareaType::class, ['mapped' => false, 'required' => false, 'label' => $this->translator->trans('form_page_content')]);
            $builder->add('keywords_'.$language, TextareaType::class, ['mapped' => false, 'required' => false, 'label' => $this->translator->trans('form_page_keywords')]);
            $builder->add('meta_description_'.$language, TextareaType::class, ['mapped' => false, 'required' => false, 'label' => $this->translator->trans('form_page_meta_description')]);
        }
        $builder->add('attachedFiles', HiddenType::class, ['required' => false, 'mapped' => false, 'attr' => ['class' => 'attachedFiles']]);
        $builder->add('active', null, ['required' => false]);
        $builder->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Page::class,
            'supported_languages' => null,
        ));
    }
}
