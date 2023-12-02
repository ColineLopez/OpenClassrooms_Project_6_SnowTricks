<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Entity\Movie;
use App\Form\MovieType;
use App\Entity\Picture;
use App\Form\PictureType;
use DateTime;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickController extends AbstractController
{
    #[Route('/home', name: 'app_tricks')]
    public function index(EntityManagerInterface $entityManager): Response
    {
         $tricks = $entityManager->getRepository(Trick::class)->findAll();

        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }

    #[Route('/trick/{slug}', name: 'view_trick')]
    public function viewTrick(EntityManagerInterface $entityManager, Request $request, string $slug): Response
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $slug]);


        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour le slug ' .$slug);
        }

        $user = $this->getUser();

        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);

        
        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $comment->setUser($user);
            $comment->setTrick($trick);
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('trick/view.html.twig', [
            'formComment' => $formComment->createView(), 
            'trick' => $trick,
            'user' => $user,
            'comment' => $comment,
        ]);
    }

    #[Route('/create-trick', name:'create_trick')]
    public function createTrick(EntityManagerInterface $entityManager, Request $request) : Response
    {
        $trick = new Trick();
        $formTrick = $this->createForm(TrickType::class, $trick);

        $formTrick->handleRequest($request);

        if ($formTrick->isSubmitted() && $formTrick->isValid()) {
            $entityManager->persist($trick);
            $entityManager->flush();
            // $trick = $form->getData();

            // return $this->redirectToRoute('task_success');

            // $formMovie = $this->createForm(MovieType::class);
            // $formMovie = $this->handleRequest($request);

            // if ($formMovie->isSubmitted() && $formMovie->isValid()) {
            //     // $movie = $formMovie->get('movies')->getData();

            //     // foreach ($movies as $movieFile) {
            //     //     $movie = new Movie();
            //     //     $picture->setTrick($Trick);

            //     //     $entityManager->persist($movie);
            //     // }

            //     // $entityManager->flush();

            // }
            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('trick/create.html.twig', [
            'formTrick' => $formTrick->createView(),
            // 'formMovie' => $formMovie->createView(),
        ]);

    }

    #[Route('/edit-trick/{slug}', name:'edit_trick')]
    public function editTrick(EntityManagerInterface $entityManager, Request $request, string $slug) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $slug]);

        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour le slug ' .$slug);
        }

        $trick->setEditDate();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('view_trick', ['slug' => $slug]);
        }

        $movie = new Movie();
        $picture = new Picture();

        return $this->render('trick/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
            'movie' => $movie,
            'picture' => $picture,
        ]);

    }

    #[Route('/delete-trick/{slug}', name:'delete_trick')]
    public function deleteTrick(EntityManagerInterface $entityManager, Request $request, string $slug) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $slug]);


        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour le slug ' .$slug);
        }

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($trick);
            $entityManager->flush();

            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('trick/delete.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);

    }
}