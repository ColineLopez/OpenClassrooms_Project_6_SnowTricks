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
    #[Route('/picture', name: 'app_picture')]
    public function index(): Response
    {
        return $this->render('picture/index.html.twig', [
            'controller_name' => 'PictureController',
        ]);
    }
    
    #[Route('/edit-trick/{id}/add-picture', name:'add_picture')]
    public function addPicture(EntityManagerInterface $entityManager, Request $request, int $id) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->find($id);

        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour l\'ID ' .$id);
        }

        $trick->setEditDate();
        // var_dump($trick);

        // $formTrick = $this->createForm(TrickType::class, $trick);
        // $formTrick->handleRequest($request);


        // if ($formTrick->getClickedButton() === $formTrick->get('save')) {
        //     // ...
        //     $entityManager->flush();
        //     return $this->redirectToRoute('view_trick', ['id' => $id]);
        // }

        // // when using nested forms, two or more buttons can have the same name;
        // // in those cases, compare the button objects instead of the button names
        // if ($formTrick->getClickedButton() === $formTrick->get('delete')){
        //     // ...
        //     $entityManager->remove($trick);
        //     $entityManager->flush();

        //     return $this->redirectToRoute('app_tricks');
        // }




        // if ($formTrick->isSubmitted() && $formTrick->isValid()) {
            // $entityManager->flush();

            // return $this->redirectToRoute('view_trick', ['id' => $id]);
        // }
        // if ($request->request->get('delete')) {
        //     $entityManager->remove($trick);
        //     $entityManager->flush();

        //     return $this->redirectToRoute('app_tricks');
        // }

        // $movie = new Movie();
        // $formMovie = $this->createForm(MovieType::class, $movie);
        // $formMovie->handleRequest($request);

        // if ($formMovie->isSubmitted() && $formMovie->isValid()) {
        //     $movie->setTrick($trick);
        //     // $trick->addMovie($movie);
        //     // $entityManager->persist($trick);
        //     $entityManager->persist($movie);
        //     $entityManager->flush();

        //     return $this->redirectToRoute('app_tricks');
        // }

        $picture = new Picture();
        $formPicture = $this->createForm(PictureType::class, $picture);
        $formPicture->handleRequest($request);

        if ($formPicture->isSubmitted() && $formPicture->isValid()) {
            $picture->setTrick($trick);
            $entityManager->persist($picture);
            $entityManager->flush();

            return $this->redirectToRoute('app_tricks');
        }

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
