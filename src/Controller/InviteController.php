<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Invite;
use App\Form\InviteFormType;
use App\Entity\Projet;
use App\Entity\User;
use Symfony\Component\Form\FormError;

class InviteController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/projet/{id}/invite', name: 'invite_create')]
    public function create(Request $request, $id): Response
    {
        $invite = new Invite();
        $form = $this->createForm(InviteFormType::class, $invite);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $form->get('email')->addError(new FormError('L\'utilisateur avec cet email n\'existe pas dans le compte gestion projet .'));
            } else {
                // Récupérer le projet en fonction de l'id
                $projet = $this->entityManager->getRepository(Projet::class)->find($id);
                $invite->setProjet($projet);
        
                // Récupérer l'utilisateur à partir du formulaire
                $invite->setUser($user);
        
                $this->entityManager->persist($invite);
                $this->entityManager->flush();
            }
        }
    
        return $this->render('invite/create.html.twig', [
            'form' => $form->createView(),
            'invite' => $invite,
        ]);
    }
}
