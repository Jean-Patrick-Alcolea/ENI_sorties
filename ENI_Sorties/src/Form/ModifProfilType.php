<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;

class ModifProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('prenom')
            ->add('nom')
            ->add('telephone', TelType::class, [
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^(\d{2} \d{2} \d{2} \d{2} \d{2})$/',
                        'message' => 'Le numéro de téléphone doit suivre le format "01 45 78 45 12"',
                    ]),
                ],
            ])
            ->add('email')
            ->add('Campus', EntityType::class, [
                'class' => Campus::class,
'choice_label' => 'nom',
            ])
            ->add('photo',
                FileType::class,
                [
                    'label'=>'Ma photo',
                    'required'=>false,
                    'mapped'=>false,
                    'constraints'=>
                        [
                            new File([
                                'maxSize'=>'1024k',
                                'mimeTypes'=> [
                                    'image/jpeg',
                                    'image/png',
                                    'image/gif'],
                                'mimeTypesMessage'=>'Format jpg, png, ou gif seulement.'
                            ])
                        ]
                ])
        ;
    }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
