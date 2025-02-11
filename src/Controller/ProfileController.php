<?php
namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\User;
use App\Form\CandidateType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(EntityManagerInterface $entityManager, Request $request, FileUploader $fileUploader): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $candidate = $user->getCandidate();

        if (!$candidate) {
            $candidate = new Candidate();
            $candidate->setUser($user);
            $entityManager->persist($candidate);
            $entityManager->flush();
        }

        if (!$user->isVerified()) {
            return $this->render('errors/not-verified.html.twig');
        }

        $formCandidate = $this->createForm(CandidateType::class, $candidate);
        $formCandidate->handleRequest($request);

        if ($formCandidate->isSubmitted() && $formCandidate->isValid()) {
            /** @var UploadedFile|null $profilePictureFile */
            $profilePictureFile = $formCandidate->get('profilePictureFile')->getData();

            /** @var UploadedFile|null $passportFile */
            $passportFile = $formCandidate->get('passportFile')->getData(); // Remplace passport par passportFile

            /** @var UploadedFile|null $cvFile */
            $cvFile = $formCandidate->get('cv')->getData();

            // Traiter l'upload de l'image de profil
            if ($profilePictureFile) {
                $profilePictureName = $fileUploader->upload($profilePictureFile, $candidate, 'profilePicture', 'profile-pictures');
                $candidate->setProfilePicture($profilePictureName);
            }

            // Traiter l'upload du passeport
            if ($passportFile) {
                $passportFileName = $fileUploader->upload($passportFile, $candidate, 'passportFile', 'passport-files');
                $candidate->setPassportFile($passportFileName); // Assurez-vous que cette méthode existe dans Candidate
            }

            // Traiter l'upload du CV
            if ($cvFile) {
                $cvFileName = $fileUploader->upload($cvFile, $candidate, 'cv', 'cv-files');
                $candidate->setCvFile($cvFileName); // Assurez-vous que cette méthode existe dans Candidate
            }

            $entityManager->persist($candidate);
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $formCandidate->createView(),
            'candidate' => $candidate,
            'originalProfilePicture' => $candidate->getProfilePicture() ?? null,
        ]);
    }
}
