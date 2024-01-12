<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use App\Entity\Comment;

class AdminController extends AbstractController
{
    protected const ACCEPTED = 1;
    protected const REJECTED = 2;
    protected const WAITING = 3;


    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function moderationPage(EntityManagerInterface $entityManager) : Response
    {
        $tricks = $entityManager->getRepository(Trick::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }

    #[Route('/admin/comment/approve/{id}', name: 'admin_approve')]
    public function approve(EntityManagerInterface $entityManager, int $id) : Response
    {
        $comment = $entityManager->getRepository(Comment::class)->find($id);

        $comment->setStatus(self::ACCEPTED);
        $entityManager->persist($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Le commentaire a bien été publié !');
        return $this->redirectToRoute('admin');
    }

    #[Route('/admin/comment/reject/{id}', name: 'admin_reject')]
    public function reject(EntityManagerInterface $entityManager, int $id): Response
    {
        $comment = $entityManager->getRepository(Comment::class)->find($id);

        $comment->setStatus(self::REJECTED);
        $entityManager->persist($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Le commentaire ne sera pas publié !');
        return $this->redirectToRoute('admin');
    }
}
