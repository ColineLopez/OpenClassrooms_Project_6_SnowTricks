<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Comment;
use App\Form\TrickType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
// use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// use Symfony\Component\Form\Extension\Core\Type\TextType;

class TrickController extends AbstractController
{
    #[Route('/tricks', name: 'app_tricks')]
    public function index(EntityManagerInterface $entityManager): Response
    {
         $tricks = $entityManager->getRepository(Trick::class)->findAll();

        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }

    #[Route('/trick/{id}', name: 'view_trick')]
    public function viewTrick(EntityManagerInterface $entityManager, int $id): Response
    {
        $trick = $entityManager->getRepository(Trick::class)->find($id);
        

        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour l\'ID ' .$id);
        }
        
        return $this->render('trick/view.html.twig', [
            'trick' => $trick,
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
            // $trick = $form->getData();

            // return $this->redirectToRoute('task_success');
            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('trick/create.html.twig', ['form' => $form->createView()]);

    }

    #[Route('/edit-trick/{id}', name:'edit_trick')]
    public function editTrick(EntityManagerInterface $entityManager, Request $request, int $id) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->find($id);


        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour l\'ID ' .$id);
        }

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('trick/edit.html.twig', ['form' => $form->createView()]);

    }

    #[Route('/delete-trick/{id}', name:'delete_trick')]
    public function deleteTrick(EntityManagerInterface $entityManager, Request $request, int $id) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->find($id);


        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour l\'ID ' .$id);
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