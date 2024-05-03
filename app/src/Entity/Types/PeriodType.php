<?php

declare(strict_types=1);

namespace App\Entity\Types;

enum PeriodType: string
{
    case MONTH = 'month';
    case YEAR = 'year';
    case QUARTER = 'quarter';
    case LIFE = 'life';
}
