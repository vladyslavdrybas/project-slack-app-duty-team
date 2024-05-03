<?php

declare(strict_types=1);

namespace App\DataTransferObject;

use App\Entity\SubscriptionPlan;
use App\Entity\User;

class SubscriptionDto
{
    public function __construct(
        public ?User $subscriber,
        public ?SubscriptionPlan $subscriptionPlan
    ) {
    }
}
