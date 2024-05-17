<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserSkills;

/**
 * @method UserSkills|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSkills|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSkills[]    findAll(array $orderBy = ['createdAt', 'DESC'], int $offset = 0, int $limit = 0)
 * @method UserSkills[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSkillsRepository extends AbstractRepository
{

}
