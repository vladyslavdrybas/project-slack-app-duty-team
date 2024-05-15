<?php

namespace App\Utility;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EmailHasher
{
    public function __construct(protected readonly ParameterBagInterface $parameterBag) {}

    public function hash(string $email): string
    {
        return hash('sha256', $email . $this->getSalt());
    }

    protected function getSalt(): string
    {
        return $this->parameterBag->get('app_secret') . $this->parameterBag->get('hash_secret');
    }
}
