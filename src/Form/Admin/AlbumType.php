<?php

namespace App\Form\Admin;

use App\Entity\Album;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlbumType extends NodeType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Album::class,
            'supported_languages' => null,
        ]);
    }
}
