<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Form\TacheFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TacheController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
#[Route('/projet/{id}/tache/creer', name: 'tache_creer')]
public function creerTache(Request $request, Projet $projet): Response
{
    $tache = new Tache();
    $form = $this->createForm(TacheFormType::class, $tache);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Associer la tâche au projet
        $tache->setProjet($projet);

        // Enregistrer la tâche dans la base de donnees
        $this->entityManager->persist($tache);
        $this->entityManager->flush();

        // Rediriger vers la page d'accueil du projet
        return $this->redirectToRoute('taches_afficher', ['id' => $projet->getId()]);

    }

    return $this->render('tache/create.html.twig', [
        'form' => $form->createView(),
        'projet' => $projet,
    ]);
    }


    #[Route('/projet/{id}/taches', name: 'taches_afficher')]
    public function afficherTaches(Projet $projet): Response
     {
        $taches = $projet->getTaches();

        return $this->render('tache/afficher.html.twig', [
        'projet' => $projet,
        'taches' => $taches,
        ]);
     }


    #[Route('/projet/{id}/supprimer/', name: 'tache_supprimer')]
     public function supprimer(Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($tache);
        $entityManager->flush();

        return $this->redirectToRoute('taches_afficher', ['id' => $tache->getProjet()->getId()]);
        }

        #[Route('/projet/{id}/edit/', name: 'tache_modifier')]
        public function modifier(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
         {
        $form = $this->createForm(TacheFormType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('taches_afficher', ['id' => $tache->getProjet()->getId()]);
        }

        return $this->render('tache/modifier.html.twig', [
            'form' => $form->createView(),
            'tache' => $tache,
        ]);
    }


}