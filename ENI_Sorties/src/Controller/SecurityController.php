<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\ModifProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\String\Slugger\SluggerInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_homepage', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('sorties_app_home');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/sorties/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/sorties/monProfil/', name: 'app_monProfil', methods: ['GET', 'POST'])]
    public function monProfil(
        ParticipantRepository $participantRepository,
        EntityManagerInterface $entityManager,
        Request $request, SluggerInterface $slugger, Filesystem $filesystem): Response
    {
        $id = $this->getUser()->getId();
        $participant= $participantRepository->find($id);
        $participantForm = $this->createForm(ModifProfilType::class, $participant);

        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted() && $participantForm->isValid())
        {
            $imageFile = $participantForm->get('photo')->getData();

            if ($imageFile)
            {
                $originalFileName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $saveFileName = $slugger->slug($originalFileName);
                $newFileName = $saveFileName.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('photo_profil_directory'),
                        $newFileName
                    );

                    if ($participant->getPhoto())
                    {
                        $oldPhotoPath = $this->getParameter('photo_profil_directory').'/'.$participant->getPhoto();
                        if ($filesystem->exists($oldPhotoPath))
                        {
                            $filesystem->remove($oldPhotoPath);
                        }
                    }
                }
                catch (FileException $e)
                {
                    $this->addFlash('error', 'Error uploading photo: '. $e->getMessage());
                    return $this->redirectToRoute('app_monProfil');
                }
                $participant->setPhoto($newFileName);
            }
            else{
                $participant->setPhoto($participant->getPhoto());
            }


            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Profil Modifié avec succes.');

            return $this->redirectToRoute('sorties_app_home');
        }


        return $this->render('security/monProfil.html.twig',
            [
                "participant"=>$participant,
                'participantForm'=>$participantForm->createView()
            ]);
    }

    #[Route(path: '/sorties/changePassword', name: 'changePassword', methods: ['GET', 'POST'])]
    public function changePassword(ParticipantRepository $participantRepository,
                                   EntityManagerInterface $entityManager,
                                   Request $request,
                                   UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $encodedPassword = $passwordHasher->hashPassword(
                $user, $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe changé');

            return $this->redirectToRoute('sorties_app_home');
        }

        return $this->render('reset_password/reset.html.twig',[
            'resetForm'=>$form
        ]);
    }

    #[Route(path: '/sorties/detailProfil/{email}', name: 'app_detailProfil', methods: ['GET'])]
    public function selectProfil(string $email,
                                 ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->findOneBy(['email'=>$email]);

        if (!$participant)
        {
            throw $this->createNotFoundException('Participant pas trouvé');
        }

        return $this->render('security/detailProfil.html.twig',
            [
                "participant"=>$participant
            ]);

    }
}
