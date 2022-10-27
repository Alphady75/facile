<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\EditProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/compte", name="user")
     */
    public function index(): Response
    {
        $user = $this->getUser();

        $statut = $user->getStatutId();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'statut' => $statut ,
        ]);
    }

    /**
     * @Route("/compte/profil", name="user.profil")
     */
    public function profil(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {   
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $newPassword = $request->get('password');
            $confirmpassword = $request->get('confirm_password');

            if ($confirmpassword == $newPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                $em->flush();
                $this->addFlash('success', 'Mot de passe modifié avec succès');
            }else{
                $this->addFlash('error', 'les deux mots de passes ne correspondent pas');
            }
        }

        return $this->render('user/profil.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('user');
        }

        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('user/login.html.twig', [
            'controller_name' => 'UserController',
            'lastUsername' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/compte/edition-du-profil", name="edit_profile")
     */
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a bien été mise à jour!');

            return $this->redirectToRoute('user');
        }

        return $this->render('user/edit_profil.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }


    
    /**
     * @Route("/nouveauclient", name="nouveau_client")
     */
    public function client(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('user');
        }
        
        //$client = $userRepository->findBy([], ['id' => 'DESC'], 1);

        if ($request->isMethod('POST')) {

            $user = new User();

            try {
                //$numero = $request->get('numero');
                $nom = $request->get('nom');
                $email = $request->get('email');
                $password = $request->get('password');
                $entreprise = $request->get('entreprise');
                $telephone = $request->get('telephone');
                $statut = $request->get('statut');
                
                $user->setNumero(12345);
                $user->setNom($nom);
                $user->setEmail($email);
                $user->setPassword($passwordHasher->hashPassword($user, $password));
                $user->setEntreprise($entreprise);
                $user->setStatut($statut);

                if($statut == "Demande envoyée") {
                    $user->setStatutId(1);
                } else if($statut == "Demande en cours de traitement") {
                    $user->setStatutId(2);
                } else if($statut == "Terminer") {
                    $user->setStatutId(3);
                }

                
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('user');

            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Un compte est déjà lié avec ce numéro de dossier ou adresse email.');
                //return $this->redirectToRoute('espace');
            }
        }
            
        return $this->render('user/inscription.html.twig', [
            //'numero' => intval($client[0]->getId()) + 1,
            'active' => 'client'
        ]);
    }
}
