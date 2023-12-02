<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Picture;
use App\Form\PictureType;

class PictureController extends AbstractController
{
    // #[Route('/picture', name: 'app_picture')]
    // public function index(): Response
    // {
    //     return $this->render('picture/index.html.twig', [
    //         'controller_name' => 'PictureController',
    //     ]);
    // }
    
    #[Route('/edit-trick/{slug}/add-picture', name:'add_picture')]
    public function addPicture(EntityManagerInterface $entityManager, Request $request, string $slug) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $slug]);

        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour le nom ' .$slug);
        }

        $trick->setEditDate();

        $picture = new Picture();
        $formPicture = $this->createForm(PictureType::class, $picture);
        $formPicture->handleRequest($request);

        if ($formPicture->isSubmitted() && $formPicture->isValid()) {
            $picture->setTrick($trick);
            $entityManager->persist($picture);
            $entityManager->flush();

            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('picture/create.html.twig', [
            'formPicture' => $formPicture->createView(),
            'trick' => $trick,
            'picture' => $picture,
        ]);

    }


    #[Route('/edit-trick/{slug}/edit-picture/{id}', name:'edit_picture')]
    public function editPicture(EntityManagerInterface $entityManager, Request $request, string $slug, int $id) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $slug]);

        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour le slug ' .$slug);
        }

        $trick->setEditDate();

        $picture = $entityManager->getRepository(Picture::class)->find($id);
        $formPicture = $this->createForm(PictureType::class, $picture);
        $formPicture->handleRequest($request);

        if ($formPicture->isSubmitted() && $formPicture->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('view_trick', ['slug' => $slug]);
        }
        

        // if ($formPicture->isSubmitted() && $formPicture->isValid()) {
        //     $picture->setTrick($trick);
        //     $entityManager->persist($picture);
        //     $entityManager->flush();

        //     return $this->redirectToRoute('view_trick', ['name' => $slug]);
        // }

        return $this->render('picture/edit.html.twig', [
            // 'formTrick' => $formTrick->createView(),
            // 'formMovie' => $formMovie->createView(), 
            'formPicture' => $formPicture->createView(),
            'trick' => $trick,
            // 'movie' => $movie,
            'picture' => $picture,
        ]);

    }


}
