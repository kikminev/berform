<?php

namespace App\Form\Admin;

use App\Document\Domain;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Document\Site;

class SiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('defaultLanguage')
            ->add('domain',
                DocumentType::class,
                [
                    'class' => Domain::class,
                    'choice_label' => function ($domain) {
                        /** @var Domain $domain */
                        return $domain->getName();
                    },
                    'required' => false,
                ]);

            foreach ($options['supported_languages'] as $language) {
                $builder->add('address_'.$language, TextType::class, ['mapped' => false]);
            }

            $builder->add('workingFrom', null, ['required' => false])
            ->add('workingTo', null, ['required' => false])
            ->add('facebook', null, ['required' => false])
            ->add('instagram', null, ['required' => false])
            ->add('twitter', null, ['required' => false])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Site::class,
            'supported_languages' => null,
        ]);
    }
}
