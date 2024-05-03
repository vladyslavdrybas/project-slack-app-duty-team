<?php

declare(strict_types=1);

namespace App\Builder;

use App\DataTransferObject\SubscriptionDto;
use App\Entity\Subscription;

class SubscriptionBuilder implements IEntityBuilder
{
    public function base(SubscriptionDto $dto): Subscription
    {
        $subscription = new Subscription();
        $subscription->setSubscriptionPlan($dto->subscriptionPlan);
        $subscription->setSubscriber($dto->subscriber);

        return $subscription;
    }
}
