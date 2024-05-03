<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use function method_exists;

abstract class AbstractVoter extends Voter
{
    abstract protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool;

    protected function isOwner(
        mixed $subject,
        User $user
    ): bool {
        if (method_exists($subject, 'getOwner')) {
            return $subject->getOwner() === $user;
        } elseif (method_exists($subject, 'getSubscriber')) {
            return $subject->getSubscriber() === $user;
        } elseif (method_exists($subject, 'getCreator')) {
            return $subject->getCreator() === $user;
        }

        return false;
    }
}
