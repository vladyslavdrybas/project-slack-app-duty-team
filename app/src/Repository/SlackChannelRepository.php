<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\SlackChannel;

/**
 * @method SlackChannel|null find($id, $lockMode = null, $lockVersion = null)
 * @method SlackChannel|null findOneBy(array $criteria, array $orderBy = null)
 * @method SlackChannel[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method SlackChannel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlackChannelRepository extends AbstractRepository
{

}
