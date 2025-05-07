<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[
                'label' => 'Nom de la sortie :',
                'required' => true
            ])
            ->add('dateHeure', DateType::class, [
                'label' => 'Date et heure de la sortie :',
                'html5' => true,
                'widget'=>'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('d/m/y'),
                    'max' => (new \DateTime('+1 year'))->format('d/m/y')
                ],
            ])
            ->add('duree', IntegerType::class,[
                'label' => 'Durée :',
                'required' => true,
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription :',
                'html5' => true,
                'widget'=>'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('d/m/y'),
                ],
            ])
            ->add('nbInscriptionsMax', IntegerType::class,[
                'label' => 'Nombre de places :',
                'required' => true,
                'attr' => [
                    'min' => 1,
                    'max' => 100
                ]
            ])
            ->add('infoSortie', TextareaType::class,[
                'label' =>'Description et infos de la sortie :',
                'required'=>true
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
'choice_label' => 'nom',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
'choice_label' => 'nom',
            ])
            ->add('Enregistrer',SubmitType::class,[
                'label'=>'Enregistrer',
                'attr'=>['class'=>'btn btn-primary']
            ])
            ->add('Publier', SubmitType::class,[
                'label'=>'Publier',
                'attr'=>['class'=>'btn btn-success']
            ])
            ->add('Supprimer', SubmitType::class,[
                'label'=>'Supprimer',
                'attr'=>['class'=>'btn btn-warning']
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
