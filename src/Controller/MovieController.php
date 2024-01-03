<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Movie;
use App\Form\MovieType;
use Symfony\Component\String\Slugger\SluggerInterface;

class MovieController extends AbstractController
{

    #[Route('/edit-trick/{slug}/add-movie', name:'add_movie')]
    public function addMovie(EntityManagerInterface $entityManager, Request $request, string $slug) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $slug]);

        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour le nom '.$slug);
        }

        $trick->setEditDate();

        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movie->setTrick($trick);
            $entityManager->persist($movie);
            $entityManager->flush();

            $this->addFlash('success', 'La vidéo a bien été ajoutée !');
            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('movie/create.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
            'movie' => $movie,
        ]);

    }

    #[Route('/edit-trick/{slug}/edit-movie/{id}', name:'edit_movie')]
    public function editMovie(EntityManagerInterface $entityManager, Request $request, string $slug, int $id) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $slug]);

        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour le slug '.$slug);
        }

        $trick->setEditDate();

        $movie = $entityManager->getRepository(Movie::class)->find($id);
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La vidéo a bien été modifiée !');
            return $this->redirectToRoute('view_trick', ['slug' => $slug]);
        }

        return $this->render('movie/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
            'movie' => $movie,
        ]);

    }

    #[Route('/edit-trick/{slug}/delete-movie/{id}', name:'delete_movie')]
    public function deleteTrick(EntityManagerInterface $entityManager, Request $request, string $slug, int $id) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $slug]);

        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour le slug '.$slug);
        }
        $movie = $entityManager->getRepository(Movie::class)->find($id);
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($movie);
            $entityManager->flush();

            $this->addFlash('success', 'La vidéo a bien été supprimée !');
            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('movie/delete.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'movie' => $movie,
        ]);

    }
}
