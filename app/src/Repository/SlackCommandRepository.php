<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\SlackCommand;

/**
 * @method SlackCommand|null find($id, $lockMode = null, $lockVersion = null)
 * @method SlackCommand|null findOneBy(array $criteria, array $orderBy = null)
 * @method SlackCommand[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method SlackCommand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlackCommandRepository extends AbstractRepository
{
}
