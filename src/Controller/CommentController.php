<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommentController extends AbstractController
{
    #[Route('/moderation', name: 'moderation')]
    public function moderationPage()
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Accès refusé. Vous devez être administrateur pour accéder à cette page.");
        }

        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }
}


