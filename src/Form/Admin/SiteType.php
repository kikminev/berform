<?php

namespace App\Form\Admin;

use App\Document\Domain;
use App\Document\User;
use App\Repository\DomainRepository;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Doctrine\ODM\MongoDB\DocumentRepository;
use phpDocumentor\Reflection\Types\This;
use Stubs\DocumentManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Document\Site;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Form\Admin\SupportedLanguageToNumberTransformer;

class SiteType extends AbstractType
{
    private $translator;
    private $param;
    private $supportedLanguageToStringTransformer;
    private $security;
    private $domainRepository;

    public function __construct(
        SupportedLanguageToNumberTransformer $supportedLanguageToStringTransformer,
        ParameterBagInterface $param,
        TranslatorInterface $translator,
        DomainRepository $domainRepository,
        Security $security
    ) {
        $this->supportedLanguageToStringTransformer = $supportedLanguageToStringTransformer;
        $this->param = $param;
        $this->translator = $translator;
        $this->security = $security;
        $this->domainRepository = $domainRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $supportedLanguages = $this->param->get('supported_languages');
        $siteActivatedLanguages = $options['supported_languages'];

        /** @var User $user */
        $user = $this->security->getUser();

        $translatedLanguages = [];
        foreach ($supportedLanguages as $key => $language) {
            $translatedLanguages[$this->translator->trans('language_label_' . $language)] = $key;
        }

        $builder->add('name', TextType::class, ['attr' => ['class' => 'slug_source']])
            ->add('slug', TextType::class, ['attr' => ['class' => 'slug_input']])
            ->add('defaultLanguage', ChoiceType::class, ['choices' => $translatedLanguages])
            ->add('supportedLanguages',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => true,
                    'choices' => $translatedLanguages,
                    'label' => $this->translator->trans('admin_site_activated_languages')
                ])
            ->add('domain',
                DocumentType::class,
                [
                    'label' => $this->translator->trans('admin_page_edit_domain'),
                    'class' => Domain::class,
                    'choices' => $this->domainRepository->findActiveByUser($user),
                    'choice_label' => function ($domain) {
                        /** @var Domain $domain */
                        return $domain->getName();
                    },
                    'required' => false,
                ]);

        $builder->get('supportedLanguages')->addModelTransformer($this->supportedLanguageToStringTransformer);

        foreach ($siteActivatedLanguages as $language) {
            $builder->add('address_' . $language, TextType::class, ['mapped' => false, 'required' => false]);
        }

        $builder->add('workingFrom', null, ['required' => false])
            ->add('workingTo', null, ['required' => false])
            ->add('email', null, ['required' => false])
            ->add('twitter', null, ['required' => false])
            ->add('linkedIn', null, ['required' => false])
            ->add('facebook', null, ['required' => false])
            ->add('instagram', null, ['required' => false])
            ->add('customCss', TextareaType::class, ['required' => false])
            ->add('customHtml', TextareaType::class, ['required' => false])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Site::class,
            'supported_languages' => null,
            'active_domains' => null,
        ]);
    }
}
