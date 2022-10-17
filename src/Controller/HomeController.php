<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/nondisponible", name="home")
     */
    public function index(ArticleRepository $articleRepository, Request $request, \Swift_Mailer $mailer): Response
    {
        $articles = $articleRepository->findBy([], ['createdAt' => 'desc'], 3);
        
        if ($request->isMethod('POST')){

            $nom = $request->get('name');
            $email = $request->get('email');
            $phone = $request->get('phone');
            $sujet = $request->get('subject');
            $description = $request->get('message');

            $message = (new \Swift_Message($sujet))
            ->setFrom('info@jvgo.be')
            ->setTo('contact@jvgo.be')
            ->setBody(
                $this->renderView(
                    // templates/emails/confirm.html.twig
                    'email/message.html.twig',[
                        'nom' => $nom,
                        'phone' => $phone,
                        'email' => $email,
                        'message' => $description,
                    ]
                ),
                'text/html'
            );

            $mailer->send($message);

            $this->addFlash('success', 'Votre message a été envoyé avec succés');

            return $this->redirectToRoute('confirmation');
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'articles' => $articles,
            'active' => 'home'
        ]);
    }

    /**
     * @Route("/a-propos", name="about")
     */
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'controller_name' => 'HomeController',
            'active' => 'about'
        ]);
    }

    /**
     * @Route("/confirmation", name="confirmation")
     */
    public function confirmation(): Response
    {
        return $this->render('home/confirmation.html.twig', [
            'controller_name' => 'HomeController',
            'active' => ''
        ]);
    }

    /**
     * @Route("/services", name="services")
     */
    public function services(): Response
    {
        return $this->render('home/services.html.twig', [
            'controller_name' => 'HomeController',
            'active' => 'services'
        ]);
    }

    /**
     * @Route("/conditions-generales-de-vente", name="cgv")
     */
    public function cgv(): Response
    {
        return $this->render('home/cgv.html.twig', [
            'controller_name' => 'HomeController',
            'active' => ''
        ]);
    }

    /**
     * @Route("/politique-de-confidentialite", name="politique")
     */
    public function politique(): Response
    {
        return $this->render('home/politique.html.twig', [
            'controller_name' => 'HomeController',
            'active' => ''
        ]);
    }

    /**
     * @Route("/mentions-legales", name="mentions")
     */
    public function mentions(): Response
    {
        return $this->render('home/mentions.html.twig', [
            'controller_name' => 'HomeController',
            'active' => ''
        ]);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function blog(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findBy([], ['createdAt' => 'desc']);

        return $this->render('home/blog.html.twig', [
            'controller_name' => 'HomeController',
            'articles' => $articles,
            'active' => 'blog'
        ]);
    }

    /**
     * @Route("/blog/{slug}-{id}", name="article", requirements={"slug": "[a-z0-9\-]*"})
     */
    public function article(Article $article): Response
    {
        return $this->render('home/article.html.twig', [
            'controller_name' => 'HomeController',
            'article' => $article,
            'active' => 'blog'
        ]);
    }
}
