<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Subscription;
use App\Entity\User;
use App\Security\Permissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use function in_array;

class SubscriptionVoter extends AbstractVoter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof Subscription) {
            return false;
        }

        if (!in_array(
            $attribute,
            [
                Permissions::READ,
            ]
        )) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param Subscription $subject
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match($attribute) {
            Permissions::READ => $this->isOwner($subject, $user),
            Permissions::UPDATE => $this->isOwner($subject, $user),
            Permissions::DELETE => $this->isOwner($subject, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }
}
