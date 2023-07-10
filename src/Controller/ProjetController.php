<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

class ProjetController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('projet/', name: 'app_home')]
    #[IsGranted('ROLE_USER')] // Verification que l'utilisateur est bien connecté
   public function index(): Response
    {
    $user = $this->getUser();
    $projets = $this->entityManager->getRepository(Projet::class)->findBy(['utilisateur' => $user]);

    return $this->render('projet/index.html.twig', [
        'projets' => $projets,
    ]);
    }

    //Création d'un nouveau projet
    #[Route('/projets/create', name: 'projet_create')]
    #[IsGranted('ROLE_USER')]
   public function create(Request $request): Response
  {
     $user = $this->getUser();
     if (!$user) {
        throw $this->createAccessDeniedException('Accès refusé. Connectez-vous pour créer un projet.');
     }

     $projet = new Projet();
     $projet->setUtilisateur($user);

     $form = $this->createForm(ProjetFormType::class, $projet);

     $form->handleRequest($request);

     if ($form->isSubmitted() && $form->isValid()) {
        $this->entityManager->persist($projet);
        $this->entityManager->flush();
       return $this->redirectToRoute('app_home');
     
     }

     return $this->render('projet/create.html.twig', [
        'form' => $form->createView(),
    ]);
}

//Suppression d'un projet
#[Route('/projets/{id}/delete', name: 'projet_delete')]
#[IsGranted('ROLE_USER')]
public function delete(Projet $projet): Response
{
    $this->entityManager->remove($projet);
    $this->entityManager->flush();

    return $this->redirectToRoute('app_home');
}


//Modification d'un porjet
#[Route('/projets/{id}/edit', name: 'projet_edit')]
#[IsGranted('ROLE_USER')]
public function edit(Request $request, Projet $projet): Response
{
    $form = $this->createForm(ProjetFormType::class, $projet);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    return $this->render('projet/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}













}    
