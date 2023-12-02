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
    
    #[Route('/edit-trick/{slug}/add-picture', name:'add_picture')]
    public function addPicture(EntityManagerInterface $entityManager, Request $request, string $slug) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $slug]);

        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour le nom ' .$slug);
        }

        $trick->setEditDate();

        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picture->setTrick($trick);
            $entityManager->persist($picture);
            $entityManager->flush();

            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('picture/create.html.twig', [
            'form' => $form->createView(),
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
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('view_trick', ['slug' => $slug]);
        }

        return $this->render('picture/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
            'picture' => $picture,
        ]);

    }

    #[Route('/edit-trick/{slug}/delete-picture/{id}', name:'delete_picture')]
    public function deleteTrick(EntityManagerInterface $entityManager, Request $request, string $slug, int $id) : Response
    {
        $trick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $slug]);


        if(!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé pour le slug ' .$slug);
        }
        $picture = $entityManager->getRepository(Picture::class)->find($id);
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($picture);
            $entityManager->flush();

            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('picture/delete.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'picture' => $picture,
        ]);

    }
}
