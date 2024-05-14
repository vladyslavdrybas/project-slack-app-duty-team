<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\SlackTeam;

/**
 * @method SlackTeam|null find($id, $lockMode = null, $lockVersion = null)
 * @method SlackTeam|null findOneBy(array $criteria, array $orderBy = null)
 * @method SlackTeam[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method SlackTeam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlackTeamRepository extends AbstractRepository
{

}
