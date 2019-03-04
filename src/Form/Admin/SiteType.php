<?php

namespace App\Form\Admin;

use App\Document\Domain;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Document\Site;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Form\Admin\SupportedLanguageToNumberTransformer;

class SiteType extends AbstractType
{
    private $translator;
    private $param;
    private $supportedLanguageToStringTransformer;

    public function __construct(
        SupportedLanguageToNumberTransformer $supportedLanguageToStringTransformer,
        ParameterBagInterface $param,
        TranslatorInterface $translator
    ) {
        $this->supportedLanguageToStringTransformer = $supportedLanguageToStringTransformer;
        $this->param = $param;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $supportedLanguages = $this->param->get('supported_languages');

        $translatedLanguages = [];
        foreach ($supportedLanguages as $key => $language) {
            $translatedLanguages[$this->translator->trans('language_label_' . $language)] = $key;
        }

        $builder
            ->add('name')
            ->add('defaultLanguage', ChoiceType::class, ['choices' => $translatedLanguages])
            ->add('supportedLanguages',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => true,
                    'choices' => $translatedLanguages,
                ])
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

        $builder->get('supportedLanguages')->addModelTransformer($this->supportedLanguageToStringTransformer);

        foreach ($supportedLanguages as $language) {
            $builder->add('address_' . $language, TextType::class, ['mapped' => false]);
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
