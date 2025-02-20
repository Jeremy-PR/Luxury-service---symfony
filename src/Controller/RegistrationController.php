<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        // Définit le type comme "candidate" par défaut
        $user->setType('candidate');

        // Créer le formulaire pour l'inscription
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Hache le mot de passe de l'utilisateur
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));


            // Création d'un profil candidat (par défaut)
            $candidate = new Candidate();
            $candidate->setUser($user);

            // Persist les deux entités
            $entityManager->persist($candidate);
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoie un email de confirmation à l'utilisateur
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('support@luxury-services.com', 'Luxury Services Support'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    // Route pour valider l'email
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository, LoggerInterface $logger): Response
    {
        $id = $request->query->get('id');
        $logger->info('User email verification requested', ['id' => $id]);

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // Validation du lien de confirmation d'email
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('app_login');
    }

    // Route pour changer un utilisateur en professionnel (pour un admin)
    #[Route('/admin', name: 'admin_set_professional')]
    public function setProfessional(int $userId, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur est un administrateur
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous devez être administrateur pour effectuer cette action.');
        }

        // Trouver l'utilisateur par son ID
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('admin_dashboard');
        }

        // Si l'utilisateur est déjà un professionnel, on le notifie
        if ($user->isProfessional()) {
            $this->addFlash('error', 'Cet utilisateur est déjà un professionnel.');
            return $this->redirectToRoute('admin_dashboard');
        }

        // Change le type de l'utilisateur à "professional"
        $user->setType('professional');

        // Sauvegarde les modifications dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'L\'utilisateur a été mis à jour en tant que professionnel.');
        return $this->redirectToRoute('admin_dashboard');
    }
}
