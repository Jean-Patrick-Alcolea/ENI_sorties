<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Filtre;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/*
    todo show 01 05 :  la "DTO" qui s'ignore: l'entité Filtre pour filtrer les données [IHM <-> [Controller <-> accès Datas]]
*/
class FiltreType extends AbstractType
{
    private CampusRepository $campusRepository;

    // Injecter le Repository dans le constructeur
    public function __construct(CampusRepository $campusRepository)
    {
        $this->campusRepository = $campusRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('choixIdCampus',
                ChoiceType::class, [
                    'choices' => $this->getListeDesCampus(),
                    'label' => 'Campus',
                    'required' => false,
                    'multiple' => false //liste déroulante avec un seul choix
            ])
            ->add('stringMotSearch',
                TextType::class,[
                    'label' => 'Le nom de la sortie contient : ',
                    'required' => false
            ])
            ->add('dateDebutSearch', DateType::class,[
                'html5' => true,//petit calendrier sympa
                'widget' => 'single_text',
                'data' => new \DateTime('-1 month'), // il y a un mois par défaut en fdébut, remplit value
                'attr' => [
                    'min' => (new \DateTime('-1 year'))->format('d-m-Y'), // Date minimale (aujourd'hui -1 an)
                    'max' => (new \DateTime('+1 year'))->format('d-m-Y'), // Date maximale (dans un an )
                ]
            ])
            ->add('dateFinSearch', DateType::class,[
                'html5' => true,//petit calendrier sympa
                'widget' => 'single_text',
                'data' => new \DateTime('+1 year'), // fin de recherche dans un an
                'attr' => [
                    'min' => (new \DateTime('-1 year'))->format('d-m-Y'), // Date minimale (aujourd'hui -1 an)
                    'max' => (new \DateTime('+1 year'))->format('d-m-Y'), // Date maximale (dans un an par exemple)
                ]
            ])
            ->add('checkUserOrganise', CheckboxType::class, [
                'label' => 'Je suis l\'organisateur',
                'required' => false, // Si la case n'est pas cochée, la valeur sera null
            ])
            ->add('checkUserInscrit', CheckboxType::class, [
                'label' => 'Je suis inscrit',
                'required' => false,
            ])
            ->add('checkSortiePassee', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filtre::class,
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
