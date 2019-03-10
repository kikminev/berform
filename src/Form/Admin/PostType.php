<?php

namespace App\Form\Admin;

use App\Document\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('slug');
        foreach ($options['supported_languages'] as $language) {
            $builder->add('title_'.$language, TextType::class, ['mapped' => false]);
            $builder->add('content_'.$language, TextareaType::class, ['mapped' => false]);
            $builder->add('keywords_'.$language, TextareaType::class, ['mapped' => false]);
            $builder->add('meta_description_'.$language, TextareaType::class, ['mapped' => false]);
        }

        $builder->add('active', null, ['required' => false]);
        $builder->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Post::class,
            'supported_languages' => null,
        ));
    }
}
