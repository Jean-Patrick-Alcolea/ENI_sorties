<?php

namespace App\Form;

use App\Entity\Campus;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class LieuType extends AbstractType
{
    private CampusRepository $campusRepository;

    // Injecter le Repository dans le constructeur
    //todo JL faire plutôt un service?
    public function __construct(CampusRepository $campusRepository)
    {
        $this->campusRepository = $campusRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

       $builder
            ->add('campus', ChoiceType::class, [
                'choices' => [
                    'choices' => $this->getListeDesCampus(),
                    'label' => 'Campus',
                    'required' => false,
                    'multiple' => false //liste déroulante avec un seul choix
                    ]
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ])
            ->add('rue')
            ->add('nom')
            ->add('codePostal')
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
            ])
            ->add('latitude', null,['required => false'])
            ->add('longitude', null, ['required => false'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }

    /**
     * Récupère la liste des campus avec un format clé - valeur
     *  sur id de campus - nom
     * mais inversé pour que le choix de "Campus X" renvoie son id.
     * @return array
     */
    public function getListeDesCampus(): array
    {
        $campusList = $this->campusRepository->findAll();

        // Formatte la liste pour la rendre compatible avec le choix du formulaire
        $formattedCampusList = [];
        foreach ($campusList as $campus) {
            $formattedCampusList[$campus->getNom()] = $campus->getId();
        }

        return $formattedCampusList;
    }
}
