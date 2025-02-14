<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/professional')]
#[IsGranted('ROLE_PROFESSIONAL')] // Restreint l'accès aux professionnels uniquement
class ProfessionalDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_professional_dashboard')]
    public function index(): Response
    {
        return $this->render('professional/dashboard.html.twig');
    }
}
