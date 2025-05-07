<?php

namespace App\Controller;

use App\Entity\Filtre;
use App\Form\FiltreType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SortieRepository;
use Symfony\Component\HttpFoundation\Response;

/*
 * todo show 01 01 le controller de l'accueil
 */
#[Route('/sorties', name: 'sorties_')]
class MainController extends AbstractController {

    
    /**
     * route vers la page principale affichant les sorties
     * @param SortieRepository $sortieRepository
     * @param EtatRepository $etatRepository
     * @param Request $request
     * @param EntityManager $entityManager
     * @return Response
     */
    #[Route (path:'/', name: 'app_home')]
    public function home(
        SortieRepository $sortieRepository,
        EtatRepository  $etatRepository,
        InscriptionRepository $inscriptionRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        //gestion du formulaire filtre
        $filtre = $this->initialiserFiltre();

        //créer le fomrulaire avec le filtre et les data du filtre
        $filtreForm = $this->createForm(FiltreType::class, $filtre, [
            'data' => $filtre,
        ]);

        //récupérer l'id user
        $userId = $this->getUser()->getId();

        $this->gererLeFormFiltre($request,$filtre,$userId,$entityManager,$filtreForm);

        //récupération des sorties
        $sortiesData = $this->prepareSortiesData(
            $sortieRepository,
            $etatRepository,
            $inscriptionRepository,
            $entityManager,
            $filtre,
            $userId);

        //envoie les données à mon twig pour afficher le tableau filtré
        return $this->render('main/home.html.twig', [
            'sortiesData' => $sortiesData,
            'filtreForm' => $filtreForm->createView()
        ]);


    }





    /* ---------- JL LES METHODES A RANGER ICI APRES LES ROUTES ----------------------- */

    /**
     * JL Permet d'initialiser le filtre avec des valeurs par défaut
     * todo show 01 02_A les deux fonctions d'initialisation et de gestion des filtres
     *
     * @return Filtre
     */
    private function initialiserFiltre(): Filtre
    {
        $filtre = new Filtre();//sera hydraté par le formulaire
        //je limite juste les dates pour l'affichage du tableau
        $filtre->setDateDebutSearch(new \DateTime('-1 month'));
        $filtre->setDateFinSearch(new \DateTime('+1 year'));
        return $filtre;
    }

    /**
     * JL Permet de préparer le formulaire pour l'affichage
     * et l'enregistrement de la recherche dans la BDD
     *
     * @param Request $request
     * @param Filtre $filtre
     * @param int $userId
     * @param EntityManagerInterface $entityManager
     * @param FormInterface $filterForm
     * @return void
     */
    private function gererLeFormFiltre(
        Request $request,
        Filtre $filtre,
        int $userId,
        EntityManagerInterface $entityManager,
        FormInterface $filterForm
    ): void
    {
        //traiter le formulaire, lignes typiques et nécessaires!:

        //injection des données du form dans $filtre:
        $filterForm->handleRequest($request);

        //les données qui ne viennent pas de l'user:

        $filtre->setIdUser($userId);
        //dater la recherche (statistique)
        $filtre->setDateFiltre(new \DateTime());

        // todo show 01 07 persistance possible des requêtes de filtres
        if($filterForm->isSubmitted()){
            //persistance du filtre si submited, pour statistiques
            $entityManager->persist($filtre);
            $entityManager->flush();//envoi à la bdd
            //todo U10 définir des conditions à la persistance? ou un nb max de filtres stockés?
        }
    }

    /**
     * todo show 01 02_B fonction de gestion des sorties avec archivage auto!
     * JL Utilise SortieRepository, fonction getSortiesForHomePage pour sortir un tableau de sortie
     * avec des infos en plus sur l'user (est-il inscrit et est-il organisateur)
     *  ainsi que le ratio des inscrits sur le max sous forme 15/30 par exemple
     *
     * @param SortieRepository $sortieRepository
     * @param EtatRepository $etatRepository
     * @param InscriptionRepository $inscriptionRepository
     * @param EntityManagerInterface $entityManager
     * @param Filtre $filtre
     * @param int $userId
     * @return array
     */
    private function prepareSortiesData( //todo show fonction de préparation des sorties pour le main tableau
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
        InscriptionRepository $inscriptionRepository,
        EntityManagerInterface $entityManager,
        Filtre $filtre,
        int $userId
    ) : array
    {
        $result = $sortieRepository->getSortiesForHomePage($filtre);//récupérer toutes les sorties + infos
        // distinguer le résultat et le message d'avertissement
        $sorties = $result['sorties'];
        $warningMessage = $result['warningMessage'];

        $sortiesData = [];//le tableau sera envoyé au twig

        // Afficher le message flash si nécessaire
        if ($warningMessage !== null) {
            $this->addFlash('warning', $warningMessage);
        }

        foreach ($sorties as $sortie) {
            // todo show 01 02_B2 L'user est-il inscrit? findOneBy
            $userIsInscrit = $inscriptionRepository->findOneBy(['participant' => $userId, 'sortie' => $sortie]) !== null;
            $userIsOrganisateur = $sortie->getParticipantOrganisateur()->getId() === $userId;

            // Ajoute le champ nombre d'inscrits sur le nombre maximum d'inscriptions
            $nbInscrits = count($sortie->getInscriptions());
            $nbMaxInscriptions = $sortie->getNbInscriptionsMax();
            $ratioInscriptions = $nbInscrits . ' / ' . $nbMaxInscriptions;

            // Ajouter les informations au tableau associatif
            $sortiesData[] = [
                'sortie' => $sortie,
                'userIsInscrit' => $userIsInscrit,
                'userIsOrganisateur' => $userIsOrganisateur,
                'ratioInscriptions' => $ratioInscriptions, // ratio type "10/50"
            ];

            // Vérifier si la sortie est finie depuis plus d'un mois
            $dateFin = $sortie->getDateHeure()->add(new \DateInterval('P1M')); // Ajoute un mois à la date de début
            $now = new \DateTime();

            // début des mises à jour auto
            // Si la date limite d'inscription est passée, mettre à jour l'état de la sortie à "Clôturée"
            if ($sortie->getDateLimiteInscription() < $now && $sortie->getEtat()->getLibelle() !== 'Clôturée') {
                $etatCloture = $etatRepository->findOneBy(['libelle' => 'Clôturée']);
                $sortie->setEtat($etatCloture);
            }

            // Si la sortie est finie depuis plus d'un mois et n'est pas déjà "Archivée", la mettre à jour
            if ($dateFin < $now && $sortie->getEtat()->getLibelle() !== 'Archivée') {
                // Mettre à jour l'état de la sortie à "Archivée"
                $etatArchivage = $etatRepository->findOneBy(['libelle' => 'Archivée']);
                $sortie->setEtat($etatArchivage);

                $currentObservations = $sortie->getAdminObservations();
                $newObservation = "Archivée automatiquement le " . (new \DateTime())->format("d/m/Y");

                if ($currentObservations !== null) {
                    // S'il y a déjà une observation, ajoute la nouvelle observation à la suite
                    $newObservation = $currentObservations . ' ' . $newObservation;
                }

                $sortie->setAdminObservations($newObservation);

                try { //todo show 01 02_B3 Usage simple de transation
                    $entityManager->beginTransaction();
                    $entityManager->persist($sortie);
                    $entityManager->flush();
                    $entityManager->commit();
                } catch (Exception $e) {
                    $entityManager->rollback();
                }
                // FIN Archive automatique si finie depuis plus d'un mois et clotûre automatique+++++++++++++++++++++++
            }
        }
        return $sortiesData;
    }
}