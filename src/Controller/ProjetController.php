<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\User;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\CoverFileUploader;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin/projet")
 * @IsGranted("ROLE_ADMIN")
 */
class ProjetController extends AbstractController
{
    /**
     * @Route("/", name="projet_index", methods={"GET"})
     */
    public function index(ProjetRepository $projetRepository): Response
    {
        return $this->render('projet/index.html.twig', [
            'projets' => $projetRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="projet_new", methods={"GET","POST"})
     */
     public function new(Request $request, CoverFileUploader $coverFileUploader): Response
     {
        //  $user=$this->getUser();
         $projet = new Projet();
         $form = $this->createForm(ProjetType::class, $projet);
         $form->handleRequest($request);
        //  $projet ->setCreateur($user);
         $projet ->setYears(new \DateTime('now'));
         
         


        if ($form->isSubmitted() && $form->isValid()) {
             $cover = $form->get('cover')->getData();
                 if ($cover){
                     $coverName = $coverFileUploader->upload($cover);
                     $projet->setCover($coverName);
                 }
                 else{
                     $projet->setCover('placeholder.jpg');
                 }

                 $entityManager = $this->getDoctrine()->getManager();
                 $entityManager->persist($projet);
                 $entityManager->flush();

            return $this->redirectToRoute('projet_index');
        }

        return $this->render('projet/new.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="projet_show", methods={"GET"})
     */
    public function show(Projet $projet): Response
    {
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="projet_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Projet $projet, CoverFileUploader $fileUploader): Response
         {
         $form = $this->createForm(ProjetType::class, $projet);
         $form->handleRequest($request);
         
             if ($form->isSubmitted() && $form->isValid()) {
                 $file = $form->get('cover')->getData();
                
                 if($file) {
                     $fileName = $fileUploader->upload($file);
                     $projet->setCover($fileName);
                 } else {
                     $file = $projet->getCover();
                     $projet->setCover($file);
                 }
                 $this->getDoctrine()->getManager()->flush();

                 return $this->redirectToRoute('projet_index');

         }

        return $this->render('projet/edit.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="projet_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Projet $projet): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projet->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($projet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('projet_index');
    }


 }
