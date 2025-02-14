<?php
namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, entity: User::class)]
#[AsEntityListener(event: Events::prePersist, entity: User::class)]
class UserRoleListener
{
    public function preUpdate(User $user, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('roles')) {
            $this->updateUserType($user);
        }
    }

    public function prePersist(User $user, LifecycleEventArgs $event): void
    {
        $this->updateUserType($user);
    }

    private function updateUserType(User $user): void
    {
        if (in_array('ROLE_PROFESSIONAL', $user->getRoles(), true)) {
            $user->setType('professional');
        } else {
            $user->setType('candidate');
        }
    }
}
