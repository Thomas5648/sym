<?php

namespace  App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('plainpassword', RepeatedType::class,[
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' =>['label' => 'Repeated password']
            ])
            ->add('fullname', TextType::class)
            ->add('termsAgreed', CheckboxType::class,[
                'mapped'=> false,
                'constraints' => new IsTrue(),
                'label' => 'Zapoznałem się z zasadami'
            ]) 
            ->add('register', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->SetDefaults([
            'data_class' => User::class
        ]);
    }
}