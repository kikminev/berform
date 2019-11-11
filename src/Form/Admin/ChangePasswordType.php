<?php

namespace App\Form\Admin;

use App\Document\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePasswordType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_password', PasswordType::class, ['mapped' => false, 'required' => true, 'label' => $this->translator->trans('password_form_old_password')])
            ->add('new_password', PasswordType::class, ['mapped' => false, 'required' => true, 'label' => $this->translator->trans('password_form_new_password')])
            ->add('new_password_repeat', PasswordType::class, ['mapped' => false, 'required' => true, 'label' => $this->translator->trans('password_form_new_password_repeat')])
            ->add('save', SubmitType::class, ['label' => $this->translator->trans('form_page_name')]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
