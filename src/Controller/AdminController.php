<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    public function __construct(UserRepository $userRepository, ArticleRepository $articleRepository) {
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {

        $clients = $this->userRepository->findAll();
        $articles = $this->articleRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'clients' => $clients,
            'articles' => $articles,
            'active' => 'home'
        ]);
    }
    
    /**
     * @Route("/dashboard/clients", name="dashboard.clients")
     */
    public function clients(): Response
    {
        $users = $this->userRepository->findBy([], ['createdAt' => 'desc']);

        return $this->render('admin/clients.html.twig', [
            'controller_name' => 'AdminController',
            'active' => 'client',
            'users' => $users
        ]);
    }
    
    /**
     * @Route("/dashboard/new-client", name="dashboard.add.client")
     */
    public function client(
        EntityManagerInterface $em, Request $request, 
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $client = $this->userRepository->findBy([], ['id' => 'DESC'], 1);

        if ($request->isMethod('POST')) {

            $user = new User();
            
            try {
                $numero = $request->get('numero');
                $nom = $request->get('nom');
                $prenom = $request->get('prenom');
                $datenaissance = $request->get('datenaissance');
                $telephone = $request->get('telephone');
                $codepostal = $request->get('codepostal');
                $ville = $request->get('ville');
                $adresse = $request->get('adresse');
                $entreprise = $request->get('entreprise');
                $activite = $request->get('activite');
                $email = $request->get('email');
                $password = $request->get('password');
                $statut = $request->get('statut');
                
                $user->setNumero($numero);
                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setAdresse($adresse);
                $user->setCodepostal($codepostal);
                $user->setVille($ville);
                $user->setDateNaissance(new \DateTime($datenaissance));
                $user->setEntreprise($entreprise);
                $user->setActivite($activite);
                $user->setEmail($email);
                $user->setPassword($passwordHasher->hashPassword($user, $password));
                $user->setStatut($statut);

                if($statut == "Demande envoyé") {
                    $user->setStatutId(1);
                } else if($statut == "Demande en cours de traitement") {
                    $user->setStatutId(2);
                } else if($statut == "Terminer") {
                    $user->setStatutId(3);
                }

                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('dashboard.clients');

            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Un compte est déjà lié avec ce numéro de dossier ou adresse email.');
                //return $this->redirectToRoute('espace');
            }
        }
            
        return $this->render('admin/new_client.html.twig', [
            'controller_name' => 'AdminController',
            'numero' => intval($client[0]->getId()) + 1,
            'active' => 'client'
        ]);
    }
    
    /**
     * @Route("/dashboard/edit-client/{id}", name="dashboard.edit.client")
     */
    public function editClient(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher, User $user): Response
    {
        if ($request->isMethod('POST')) {

            try {
                $numero = $request->get('numero');
                $nom = $request->get('nom');
                $prenom = $request->get('prenom');
                $datenaissance = $request->get('datenaissance');
                $telephone = $request->get('telephone');
                $codepostal = $request->get('codepostal');
                $ville = $request->get('ville');
                $adresse = $request->get('adresse');
                $entreprise = $request->get('entreprise');
                $activite = $request->get('activite');
                $email = $request->get('email');
                $password = $request->get('password');
                $statut = $request->get('statut');
                
                $user->setNumero($numero);
                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setAdresse($adresse);
                $user->setCodepostal($codepostal);
                $user->setVille($ville);
                $user->setDateNaissance(new \DateTime($datenaissance));
                $user->setEntreprise($entreprise);
                $user->setActivite($activite);
                $user->setEmail($email);
                if($password) {
                    $user->setPassword($passwordHasher->hashPassword($user, $password));
                }
                $user->setStatut($statut);

                if($statut == "Demande envoyée") {
                    $user->setStatutId(1);
                } else if($statut == "Demande en cours de traitement") {
                    $user->setStatutId(2);
                } else if($statut == "Terminer") {
                    $user->setStatutId(3);
                }

                $em->flush();

                return $this->redirectToRoute('dashboard.clients');
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Un compte est déjà lié avec ce numéro de dossier ou adresse email.');
                //return $this->redirectToRoute('espace');
            }
        }
            
        return $this->render('admin/edit_client.html.twig', [
            'controller_name' => 'AdminController',
            'active' => 'client',
            'user' => $user
        ]);
    }

    /**
     * @Route("/dashboard/delete-client/{id}", name="dashboard.delete.client", methods={"DELETE"})
     * @param User $user
     * @return RedirectResponse
     */
    public function deleteClient(User $user, Request $request, EntityManagerInterface $em)
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(),$request->get('_token') )){
            $em->remove($user);
            $em->flush();
        }
        return $this->redirectToRoute('dashboard.clients');
    }
    
    /**
     * @Route("/dashboard/articles", name="dashboard.articles")
     */
    public function articles(): Response
    {
        $articles = $this->articleRepository->findBy([], ['createdAt' => 'desc']);

        return $this->render('admin/articles.html.twig', [
            'controller_name' => 'AdminController',
            'active' => 'blog',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/dashboard/addarticle", name="dashboard.addarticle")
     */
    public function addArticle(Request $request, EntityManagerInterface $em)
    {
        $article = new Article(); 

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        
        if ($request->isMethod('POST') && $form->isValid())
        {          
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('dashboard.articles');
        }

        return $this->render('admin/add_article.html.twig', [
            'controller_name' => 'AdminController',
            'active' => 'blog',
            'edit' => false,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dashboard/editarticle/{id}", name="dashboard.editarticle")
     */
    public function editArticle(Article $article, Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        
        if ($request->isMethod('POST') && $form->isValid())
        {          
            $em->flush();
        }

        return $this->render('admin/add_article.html.twig', [
            'controller_name' => 'AdminController',
            'active' => 'blog',
            'edit' => true,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dashboard/delete-article/{id}", name="dashboard.delete.article", methods={"DELETE"})
     * @param Article $article
     * @return RedirectResponse
     */
    public function deleteArticle(Article $article, Request $request, EntityManagerInterface $em)
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(),$request->get('_token') )){
            $em->remove($article);
            $em->flush();
        }
        return $this->redirectToRoute('dashboard.articles');
    }
}
