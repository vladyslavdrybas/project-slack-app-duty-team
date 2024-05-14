<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\SlackUser;

/**
 * @method SlackUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method SlackUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method SlackUser[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method SlackUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlackUserRepository extends AbstractRepository
{

}
