<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\SlackCommand;
use App\Services\DutyTeamSlackBot\Config\CommandList;

/**
 * @method SlackCommand|null find($id, $lockMode = null, $lockVersion = null)
 * @method SlackCommand|null findOneBy(array $criteria, array $orderBy = null)
 * @method SlackCommand[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method SlackCommand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlackCommandRepository extends AbstractRepository
{
    public function loadAll(string $order = 'ASC', int $limit = 100, int $offset = 0): array
    {
        return $this->findBy(
            [
                'commandName' => CommandList::SkillsAdd,
            ],
            [
                'createdAt' => $order,
            ],
            $limit,
            $offset
        );
    }
}
