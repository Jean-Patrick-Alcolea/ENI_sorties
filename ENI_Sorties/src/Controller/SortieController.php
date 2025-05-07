<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\ModifSortieType;
use App\Form\SortieCRUDType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Sortie;
use App\Form\SortieType;
use function Symfony\Component\Clock\now;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/sorties', name: 'sorties_')]
class SortieController extends AbstractController
{
    //Route vers le détail des sorties
    #[Route (path:'/nouvelleSortie', name: 'nouvelleSortie', methods: ['GET', 'POST'])]
    public function new (
        Request $request,
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository
    ): Response
    {
        $sortie = new Sortie();
        $user= $this->getUser();

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if ($sortieForm->getClickedButton() && 'Enregistrer' === $sortieForm->getClickedButton()->getName()){
                $etatCree = $etatRepository->find(1);
                $sortie->setEtat($etatCree);

                $sortie->setParticipantOrganisateur($user);

                // Traitement du formulaire et sauvegarde en base de données
                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie créée');

                return $this->redirectToRoute('sorties_app_home');
            }
        elseif ($sortieForm->getClickedButton() && 'Publier' === $sortieForm->getClickedButton()->getName()){
                $etatPubliee = $etatRepository->find(2);
                $sortie->setEtat($etatPubliee);
            $sortie->setParticipantOrganisateur($user);

            // Traitement du formulaire et sauvegarde en base de données
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie Publiée');

            return $this->redirectToRoute('sorties_app_home');
            }
        }

        return $this->render('vue_sorties/creer_sortie.html.twig', [
            'sortieForm'=>$sortieForm->createView(),
        ]);
    }

    // todo show 02 01_01 route pour l'inscription à une sortie
    /**
     * Inscription de l'user à la sortie.
     * Il faut la date limite ne soit pas dépassée et que le nombre d'inscrit ne soit pas au max
     * @param int $id
     * @param Sortie $sortie
     * @param SortieRepository $sortieRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/sinscrire/{id}', name: 'sinscrire')]
    public function sinscrire(
        int $id,
        Sortie $sortie,
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        //choper l'user connecté et la sortie
        $user = $this->getUser();

        //Vérofier que le statut est 'Ouverte' pour les inscriptions'
        if ($sortie->getEtat()->getLibelle() !== 'Ouverte') {
            $this->addFlash('warning', 'La sortie n\'est pas ouverte pour les inscriptions.');
            return $this->redirectToRoute('sorties_show', ['id' => $sortie->getId()], Response::HTTP_SEE_OTHER);
        }

        //vérifier la date max d'inscription
        $now = new \DateTime();
        if ($now > $sortie->getDateLimiteInscription()) {
            $this->addFlash('warning', 'La date limite d\'inscription est dépassée 🦥');
            return $this->redirectToRoute('sorties_show', ['id' => $sortie->getId()], Response::HTTP_SEE_OTHER);
        }

        // Vérifier si l'utilisateur est déjà inscrit
        $inscriptionExistante = $entityManager->getRepository(Inscription::class)
            ->findOneBy(['participant' => $user, 'sortie' => $sortie]);
        //si déjà inscrit il se prend un flash dans la tronchetta
        if ($inscriptionExistante) {
            $this->addFlash('warning', 'Tu êtes déjà inscrit à cette sortie 🏴‍☠️');
            return $this->redirectToRoute('sorties_show', ['id' => $sortie->getId()], Response::HTTP_SEE_OTHER);
        }

        //créer une instance d'Inscription
        $newInscription = new Inscription();
        $newInscription->setParticipant($user);
        $newInscription->setSortie($sortie);
        $newInscription->setDateInscription(new \DateTime());//aujourd'hui mon frère

        //persister l'inscription
        $entityManager->persist($newInscription);
        $entityManager->flush();//envoi à la bdd

        $this->addFlash('success', 'Tu êtes bien inscrit à cette sortie  😎️');

        return $this->redirectToRoute('sorties_show', ['id' => $sortie->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/get-lieux-by-ville', name: 'lieuxByVille')]
    public function getLieuxByVille(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $villeId = $request->get('villeId');

        $lieux = $entityManager->getRepository(Lieu::class)->findBy(['ville'=>$villeId]);
        $options = [];
        foreach ($lieux as $lieu){
            $options[$lieu->getId()]= $lieu->getNom();
        }
        return new JsonResponse($options);
    }

    #[Route (path: '/get-cp-by-ville', name: 'getCpByVille')]
    public function getCpByVille(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $villeId = $request->get('villeId');

        $ville = $entityManager->getRepository(Ville::class)->find($villeId);

        $codePostal = [];

        $codePostal = ['codePostal' => $ville->getCodePostal()];

        return new JsonResponse($codePostal);

    }


    #[Route (path: '/get-lieu-params', name: 'getLieuParams')]
    public function getLieuParams(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $lieuId = $request->get('lieuId');

        $lieu = $entityManager->getRepository(Lieu::class)->find($lieuId);

        $params =[];

        $params = ['rue'=>$lieu->getRue(),'long'=>$lieu->getLongitude(), 'lat'=>$lieu->getLatitude()];

        return new JsonResponse($params);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(
        Sortie $sortie,
    ): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);

        /* JL j'ajoute la liste des participants */

        // la liste des inscriptions:
        $listeInscriptions = $sortie->getInscriptions();

        //la liste des participants correspondants en bouclant sur chaque inscription
        $participants = []; //ce tableau sera envoyé au twig
        foreach($listeInscriptions as $inscription){
            $participants[] = [
                'leParticipant' => $inscription->getParticipant(),
                'laDateDInscription' => $inscription->getDateInscription()];
        }

        //envoi à show
        return $this->render('vue_sorties/lecture_sortie.html.twig', [
            'sortie' => $sortie,
            'participants' => $participants,
            'form' => $form
        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,
                         int $id,
                         EntityManagerInterface $entityManager,
                         SortieRepository $sortieRepository,
                         LieuRepository $lieuRepository,
                         VilleRepository $villeRepository, EtatRepository $etatRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        $lieu = $lieuRepository->find($sortie->getLieu()->getId());
        $ville = $villeRepository->find($lieu->getVille()->getId());
        $sortieForm = $this->createForm(ModifSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if ($sortieForm->getClickedButton() && 'Enregistrer' === $sortieForm->getClickedButton()->getName()){
                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie Modifiée');

                return $this->redirectToRoute('sorties_app_home');
            }
        elseif ($sortieForm->getClickedButton() && 'Publier' === $sortieForm->getClickedButton()->getName()){
            $etatPubliee = $etatRepository->find(2);
            $sortie->setEtat($etatPubliee);

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie Publiée');

            return $this->redirectToRoute('sorties_app_home');
        }
            elseif ($sortieForm->getClickedButton() && 'Supprimer' === $sortieForm->getClickedButton()->getName()){
                $entityManager->remove($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie supprimée');

                return $this->redirectToRoute('sorties_app_home');
            }

        }

        //JL todo show ajouter la condition d'affichage du bouton d'annulation
        $annulable = false;

        $etat = $sortie->getEtat();

        if ($etat !== null) {
            $libelleEtat = $etat->getLibelle();
            dump("Libellé = $libelleEtat");
        } else {
            dump("Libellé non récupéré");
            $libelleEtat = "nop";
        }

        $dateLim = $sortie->getDateLimiteInscription();
        if(
            $libelleEtat == "Ouverte" && $dateLim > new \DateTime()
        ){
            $annulable = true;
        }

        return $this->render('vue_sorties/ecriture_sortie.html.twig', [
            'sortie' => $sortie,
            'lieu'=>$lieu,
            'ville'=>$ville,
            'sortieForm' => $sortieForm->createView(),
            'annulable' => $annulable
        ]);
    }


    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sorties_app_home', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * JL fonction pour de désister d'une sortie, cela se passe sur la page d'accueil
     * todo show 02 01_02 désistement d'une sortie
     *
     * @param Sortie $sortie
     * @param SortieRepository $sortieRepository
     * @param InscriptionRepository $inscriptionRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route(path: '/desister/{id}', name: 'desister', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function desister(

        Sortie $sortie,
        SortieRepository $sortieRepository,
        InscriptionRepository $inscriptionRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        //choper l'user connecté et la sortie
        $user = $this->getUser();

        //Vérifier que l'user est inscrit (normalement déjà contrôllé à l'affichage du lien mais par sécurité)
        $inscriptionExistante = $inscriptionRepository->findOneBy(['participant' => $user, 'sortie' => $sortie]);
        //géniales ces fonctions

        // Si l'inscription existe, la supprimer
        if ($inscriptionExistante) {
            $entityManager->remove($inscriptionExistante);
            $entityManager->flush();

            $this->addFlash('success', 'Désinscription réussie ! 🫡');
        } else { //je le rappelle, normalement cette erreur n'est pas possible
            $this->addFlash('warning', 'Tu n\'es pas inscrit à cette sortie. 🤔');
        }

        //renvoie simplement la page d'accueil avec le X mis à jour et le Flash relatif à l'action menée
        return $this->redirectToRoute('sorties_app_home', [], Response::HTTP_SEE_OTHER);
    }

    // todo show 03 02 annulation d'une sortie, formulaire pour ajouter une observation
    /**
     * JL affichage du formulaire pour indiquer le motif et valider l'annulation
     *
     * @param Sortie $sortie
     * @return Response
     */
    #[Route(path: '/annulationform/{id}', name:"annulation_form", methods: ['POST'])]
    public function annulationForm(
        Sortie $sortie,
        Request $request,
    ): Response
    {
        //Vérif du token CSRF
        if(!$this->isCsrfTokenValid('annulation_form'.$sortie->getId(), $request->request->get('_token'))){
            $this->addFlash('warning', "Token CSRF invalid");
            return $this->redirectToRoute('sorties_app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vue_sorties/annulation.html.twig', ['sortie' => $sortie]);

    }

    //todo show 03 04 validation de l'annulation, ajout du motif.
    /**
     * JL
     *
     * @param Request $request
     * @param Sortie $sortie
     * @param EtatRepository $etatRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route(path: '/annulation/{id}', name: 'annulation_confirmation', methods: ['POST'])]
    public function annulationConfirmation(
        Request $request,
        Sortie $sortie,
        EtatRepository $etatRepository,
        EntityManagerInterface $entityManager): Response
    {
        $motif = $request->request->get('motif');

        if ($this->isCsrfTokenValid('annulation_confirmation'.$sortie->getId(), $request->request->get('_token'))) {

            // Enregistre le motif d'annulation dans le champ adminObservations de la sortie
            $currentObservations = $sortie->getAdminObservations();

            if ($currentObservations !== null) {
                // S'il y a déjà une observation, ajoute la nouvelle observation à la suite
                $newObservation = $currentObservations . ' ' . $motif;
            }else{
                $newObservation = $motif;
            }

            $sortie->setAdminObservations($newObservation);

            // Suppression toutes les inscriptions associées à cette sortie
            foreach ($sortie->getInscriptions() as $inscription) {
                $entityManager->remove($inscription); // pas de persist pour les remove
            }

            // Rend la sortie annulée
            $etatArchivage = $etatRepository->findOneBy(['libelle' => 'Annulée']);
            $sortie->setEtat($etatArchivage);

            // persistence
            $entityManager->persist($sortie);
            $entityManager->flush();
        }
        //dernière étape de l'annulation temrinée
        $this->addFlash('success', 'Sortie annulée');
        return $this->redirectToRoute('sorties_app_home', [], Response::HTTP_SEE_OTHER);
    }




}