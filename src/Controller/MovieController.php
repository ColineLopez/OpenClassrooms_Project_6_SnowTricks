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

class MovieController extends AbstractController
{
    // #[Route('/movie', name: 'app_movie')]
    // public function index(): Response
    // {
    //     return $this->render('movie/index.html.twig', [
    //         'controller_name' => 'MovieController',
    //     ]);
    // }

    #[Route('/edit-trick/{slug}/add-movie', name:'add_movie')]
    public function addMovie(EntityManagerInterface $entityManager, Request $request, string $slug) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $slug]);

        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour le nom ' .$slug);
        }

        $trick->setEditDate();

        $movie = new Movie();
        $formMovie = $this->createForm(MovieType::class, $movie);
        $formMovie->handleRequest($request);

        if ($formMovie->isSubmitted() && $formMovie->isValid()) {
            $movie->setTrick($trick);
            $entityManager->persist($movie);
            $entityManager->flush();

            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('movie/create.html.twig', [
            'formMovie' => $formMovie->createView(),
            'trick' => $trick,
            'movie' => $movie,
        ]);

    }

    #[Route('/edit-trick/{slug}/edit-movie/{id}', name:'edit_movie')]
    public function editMovie(EntityManagerInterface $entityManager, Request $request, string $slug, int $id) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $slug]);

        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour le slug ' .$slug);
        }

        $trick->setEditDate();

        $movie = $entityManager->getRepository(Movie::class)->find($id);
        $formMovie = $this->createForm(MovieType::class, $movie);
        $formMovie->handleRequest($request);

        if ($formMovie->isSubmitted() && $formMovie->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('view_trick', ['slug' => $slug]);
        }

        return $this->render('movie/edit.html.twig', [
            'formMovie' => $formMovie->createView(),
            'trick' => $trick,
            'movie' => $movie,
        ]);

    }
}
