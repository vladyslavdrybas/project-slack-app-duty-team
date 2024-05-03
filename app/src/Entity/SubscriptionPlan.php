<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Types\PeriodType;
use App\Repository\SubscriptionPlanRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SubscriptionPlanRepository::class, readOnly: false)]
#[ORM\Table(name: "subscription_plan")]
#[ORM\UniqueConstraint(
    name: 'title_region_country_period',
    columns: ['title', 'region', 'country', 'period']
)]
#[UniqueEntity(fields: ['title', 'region', 'country', 'period'])]
class SubscriptionPlan extends AbstractEntity
{
    #[ORM\Column(name: "title", type: Types::STRING, length: 36)]
    protected string $title;

    #[ORM\Column(name: "description", type: Types::STRING, length: 256, unique: false, nullable: true)]
    protected string $description;

    #[ORM\Column(name: "region", type: Types::STRING, length: 3, options: ['default' => 'ALL'])]
    protected string $region = 'ALL';

    # ISO 3166-1 alpha-3
    #[ORM\Column(name: "country", type: Types::STRING, length: 3, options: ['default' => 'ALL'])]
    protected string $country = 'ALL';

    # ISO 4217
    #[ORM\Column(name: "currency", type: Types::STRING, length: 3, options: ['default' => 'USD'])]
    protected string $currency = 'USD';

    # cents
    #[ORM\Column(name: "price", type: Types::INTEGER, length: 20)]
    protected string $price;

    # month, year
    #[ORM\Column(name: "period", type: Types::STRING, length: 7, enumType: PeriodType::class)]
    protected PeriodType $period = PeriodType::MONTH;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    /**
     * @return \App\Entity\Types\PeriodType
     */
    public function getPeriod(): PeriodType
    {
        return $this->period;
    }

    /**
     * @param \App\Entity\Types\PeriodType $period
     */
    public function setPeriod(PeriodType $period): void
    {
        $this->period = $period;
    }
}
