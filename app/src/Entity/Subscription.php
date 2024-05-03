<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class, readOnly: false)]
#[ORM\Table(name: "subscription")]
#[ORM\UniqueConstraint(
    name: 'subscriber_subscription_plan',
    columns: ['subscriber_id', 'subscription_plan_id']
)]
#[UniqueEntity(fields: ['subscriber', 'subscriptionPlan'])]
class Subscription extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name:'subscriber_id', referencedColumnName: 'id', nullable: true)]
    protected User $subscriber;

    #[ORM\ManyToOne(targetEntity: SubscriptionPlan::class)]
    #[ORM\JoinColumn(name:'subscription_plan_id', referencedColumnName: 'id', nullable: true)]
    protected SubscriptionPlan $subscriptionPlan;

    #[ORM\Column(name: "endDate", type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTimeInterface $endDate = null;

    #[ORM\Column(name: "is_payed", type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    protected bool $isPayed = false;

    /**
     * @return \App\Entity\User
     */
    public function getSubscriber(): User
    {
        return $this->subscriber;
    }

    /**
     * @param \App\Entity\User $subscriber
     */
    public function setSubscriber(User $subscriber): void
    {
        $this->subscriber = $subscriber;
    }

    /**
     * @return \App\Entity\SubscriptionPlan
     */
    public function getSubscriptionPlan(): SubscriptionPlan
    {
        return $this->subscriptionPlan;
    }

    /**
     * @param \App\Entity\SubscriptionPlan $subscriptionPlan
     */
    public function setSubscriptionPlan(SubscriptionPlan $subscriptionPlan): void
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * @param \DateTimeInterface|null $endDate
     */
    public function setEndDate(?DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return bool
     */
    public function isPayed(): bool
    {
        return $this->isPayed;
    }

    /**
     * @param bool $isPayed
     */
    public function setIsPayed(bool $isPayed): void
    {
        $this->isPayed = $isPayed;
    }
}
