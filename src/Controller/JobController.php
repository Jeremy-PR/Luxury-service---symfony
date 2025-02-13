<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Entity\User;
use App\Form\JobOfferType; // Assure-toi que tu as ce formulaire
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class JobController extends AbstractController
{
  
     
    #[Route('/job', name: 'app_job')]
    public function index(): Response
    {
        return $this->render('job/index.html.twig', [
           
        ]);
    }


    #[Route('/job/{slug}', name: 'app_job_show')]
    public function show(string $slug): Response
    {
      
        return $this->render('job/show.html.twig', [
            'slug' => $slug,
        ]);
    }

    // Route pour permettre à un professionnel de créer une offre
    #[Route('/job/create', name: 'app_create_job')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {


        // Vérifie que l'utilisateur est un professionnel
        if (!$this->isGranted('ROLE_PROFESSIONAL')) {
            throw new AccessDeniedException('Accès réservé aux professionnels.');
        }

        // Création de l'objet JobOffer
        $jobOffer = new JobOffer();

        // Création du formulaire pour créer une offre
        $form = $this->createForm(JobOfferType::class, $jobOffer);

        // Gestion de la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Lier l'offre au professionnel connecté (l'utilisateur courant)
            $user = $this->getUser();
            if ($user === null) {
                throw new \LogicException('User not found.');
            }
            $jobOffer->setUser((string) $user);

            // Persister l'offre d'emploi dans la base de données
            $entityManager->persist($jobOffer);
            $entityManager->flush();

            // Redirige vers le tableau de bord professionnel (ou autre page)
            // return $this->redirectToRoute('professional_dashboard');  // Assure-toi que la route 'professional_dashboard' existe
        }
        
        
        // Si le formulaire n'est pas soumis ou valide, on affiche le formulaire
        return $this->render('job/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }



}
