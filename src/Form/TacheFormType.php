<?php

namespace App\Form;



use App\Entity\Tache;
use App\Entity\Priorite;
use App\Entity\Statut;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TacheFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la tache',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la tache',
            ])
            ->add('dateEcheance', DateType::class, [
                'label' => 'Date d\'écheance',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy', 
                'attr' => ['class' => 'datepicker'],
            ])
            ->add('priorite', EntityType::class, [
                'label' => 'Priorite',
                'class' => Priorite::class,
                'choice_label' => 'nom',
            ])
            ->add('statut', EntityType::class, [
                'label' => 'Statut',
                'class' => Statut::class,
                'choice_label' => 'nom',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
