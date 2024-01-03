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
    protected const ACCEPTED = 1;
    protected const REJECTED = 2;
    protected const WAITING = 3;

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
            $comment->setStatus(self::WAITING);
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a bien été envoyé !');
            return $this->redirectToRoute('view_trick', ['slug' => $slug]);
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
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trick);
            $entityManager->flush();
            
            $this->addFlash('success', 'Figure créée !');
            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('trick/create.html.twig', [
            'form' => $form->createView(),
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

            $this->addFlash('success', 'Figure modifiée !');
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
            throw $this->createNotFoundException('Aucun trick trouvé pour le slug '.$slug);
        }

        // $form = $this->createForm(TrickType::class, $trick);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($trick);
            $entityManager->flush();

            $this->addFlash('success', 'La figure a bien été supprimée !');
            return $this->redirectToRoute('app_tricks');
        }

        // return $this->render('trick/delete.html.twig', [
            // 'trick' => $trick,
            // 'form' => $form->createView()
        // ]);

    // }
}