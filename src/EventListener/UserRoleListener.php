<?php
namespace App\EventListener;

use App\Entity\User;
use App\Entity\Professional;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, entity: User::class)]
#[AsEntityListener(event: Events::prePersist, entity: User::class)]
class UserRoleListener
{
    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('roles')) {
            $this->updateUserType($user, $event->getObjectManager());
        }
    }

    public function prePersist(User $user, PrePersistEventArgs $event): void
    {
        $this->updateUserType($user, $event->getObjectManager());
    }

    private function updateUserType(User $user, $entityManager): void
    {
        if (in_array('ROLE_PROFESSIONAL', $user->getRoles(), true)) {
            $user->setType('professional');

            if (!$user->getProfessional()) {
                $professional = new Professional();
                $professional->setUser($user);
                $professional->setCompanyName('Entreprise non définie');
                $entityManager->persist($professional);
            }
        } else {
            $user->setType('candidate');
        }
    }
}
