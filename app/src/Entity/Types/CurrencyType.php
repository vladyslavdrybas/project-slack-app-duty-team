<?php

namespace App\Entity\Types;

enum CurrencyType: string
{
    case MSC = 'MSC'; # Micro SaaS Coin
    case USD = 'USD';
    case EUR = 'EUR';
    case UAH = 'UAH';
}
