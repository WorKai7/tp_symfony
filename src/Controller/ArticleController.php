<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }


    #[Route('/article/creer', 'creer_article')]
    public function create(EntityManagerInterface $entityManager,
                            Request $request,
                            FileUploader $fileUploader): Response {
        $article = new Article();

        $form = $this->createForm(ArticleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageFilename = $fileUploader->upload($imageFile);
                $article->setImageFilename($imageFilename);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', "Article " . $article->getId() . " à été inséré avec succès !");
        }


        return $this->render('article/creer.html.twig', [
            'form' => $form
        ]);
    }


    #[Route('article/lire', 'lire_article')]
    public function show(ArticleRepository $articleRepository): Response {
        $articles = $articleRepository->findBy([], ['id' => 'ASC']);

        return $this->render('article/lire.html.twig', ['articles' => $articles]);
    }


    #[Route('article/supprimer/{id}', 'supprimer_article')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response {
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException("Pas de produit trouvé pour l'id $id");
        }

        $entityManager->remove($article);
        $entityManager->flush();

        return  $this->redirectToRoute('lire_article');
    }


    #[Route('article/update/{id}', 'update_article')]
    public function update(EntityManagerInterface $entityManager, int $id, Request $request, FileUploader $fileUploader): Response {
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException("Pas de produit trouvé pour l'id $id");
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageFilename = $fileUploader->upload($imageFile);
                $article->setImageFilename($imageFilename);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', "Article " . $article->getId() . " à été modifié avec succès !");
        }

        return $this->render('article/creer.html.twig', [
            'form' => $form
        ]);
    }
}
