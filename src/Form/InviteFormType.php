<?php

namespace App\Form;


use App\Entity\Invite;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class InviteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => "Email de l'invite",
                'constraints' => [
                    new Callback([$this, 'validateEmail']),
                ],
            ])
            ->add('user', EntityType::class, [
                'label' => 'Utilisateur',
                'class' => User::class,
                'choice_label' => 'email',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Invite::class,
        ]);
    }
    
    public function validateEmail($value, ExecutionContextInterface $context)
    {
        $user = $context->getRoot()->getData()->getUser();
        
        if ($user === null && $value !== null) {
            $context->buildViolation('L\'utilisateur avec cet email n\'existe pas.')
                ->atPath('email')
                ->addViolation();
        }
    }
}
