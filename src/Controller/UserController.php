<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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
     * @Route("/", name="user")
     */
    public function index(): Response
    {
        $user = $this->getUser();

        if(!$user){
            return $this->redirectToRoute('login');
        }

        $statut = $user->getStatutId();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'statut' => $statut ,
            'active' => ''
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
            'active' => ''
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('user/login.html.twig', [
            'controller_name' => 'UserController',
            'lastUsername' => $lastUsername,
            'error' => $error,
            'active' => ''
        ]);
    }


    
    /**
     * @Route("/nouveauclient", name="neveau_client")
     */
    public function client(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        $client = $userRepository->findBy([], ['id' => 'DESC'], 1);

        if ($request->isMethod('POST')) {

            $user = new User();

            try {
                $numero = $request->get('numero');
                $nom = $request->get('nom');
                $email = $request->get('email');
                $password = $request->get('password');
                $entreprise = $request->get('entreprise');
                $telephone = $request->get('telephone');
                $statut = $request->get('statut');
                
                $user->setNumero($numero);
                $user->setNom($nom);
                $user->setEmail($email);
                $user->setPassword($passwordHasher->hashPassword($user, $password));
                $user->setEntreprise($entreprise);
                $user->setStatut($statut);

                if($statut == "Demande envoyer") {
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
            'numero' => 1556, //intval($client[0]->getId()) + 1,
            'active' => 'client'
        ]);
    }
}
