<?php

namespace App\Form\Admin;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Document\Node;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Contracts\Translation\TranslatorInterface;


class ShotType extends NodeType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct($translator);
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['supported_languages'] as $language) {
            $builder->add('content_' . $language,
                TextareaType::class,
                [
                    'mapped' => false,
                    'required' => false,
                    'label' => $this->translator->trans('admin_form_page_description'),
                ]);
        }

        $builder->add('attachedFiles',
            HiddenType::class,
            ['required' => false, 'mapped' => false, 'attr' => ['class' => 'attachedFiles']]);
        $builder->add('active', null, ['required' => false]);
        $builder->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Node::class,
            'supported_languages' => null,
        ]);
    }
}
