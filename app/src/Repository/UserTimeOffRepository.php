<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserTimeOff;

/**
 * @method UserTimeOff|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTimeOff|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTimeOff[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method UserTimeOff[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTimeOffRepository extends AbstractRepository
{

}
