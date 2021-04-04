<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\User;
use App\Form\SearchProjetType;
use App\Repository\ProjetRepository;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtistesController extends AbstractController
{
    /**
     * @Route("/artistes", name="artistes")
     */
    public function index(PaginatorInterface $paginator, Request $request, ProjetRepository $projetRepository): Response
    {
        $search_form = $this->createForm(SearchProjetType::class);
        $search_form -> handleRequest($request);
        $data = $this->getDoctrine()->getRepository(Projet::class)->findAll();
        $projetList = $paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            12
        );
        if ($search_form->isSubmitted() && $search_form->isValid())
             {
                 $value = $search_form->getData();
                 
                $dataList = $projetRepository->findByName($value);
                // dd($dataList);
                $projetList = $paginator->paginate(
                    $dataList,
                    $request->query->getInt('page',1),
                    // on passe un large int pour eviter la pagination sur la réponse du form car le changement de page annule la recherche. 
                    2048
                );
                return $this->render('artistes/index.html.twig', [
                    'projet' => $projetList,
                    'form' => $search_form -> createView(),
                ]);
             }

        return $this->render('artistes/index.html.twig', [
            'controller_name' => 'ArtistesController',
            'projet' => $projetList,
            'form' => $search_form -> createView(),
        ]);
    }
}