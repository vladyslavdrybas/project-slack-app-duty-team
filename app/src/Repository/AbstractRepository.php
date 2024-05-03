<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Exceptions\AlreadyExists;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use function str_replace;

/**
 * @method EntityInterface|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntityInterface|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntityInterface[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class AbstractRepository extends ServiceEntityRepository implements Selectable
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityClass = substr(str_replace('Repository', 'Entity', static::class), 0,-6);
        /** @phpstan-ignore-next-line */
        parent::__construct($registry, $entityClass);
    }

    public function add(EntityInterface $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function remove(EntityInterface $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function save(): void
    {
        try {
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            $message = $e->getMessage();
            if (str_contains($message, 'duplicate key')) {
                $message = 'Already exists.';
                throw new AlreadyExists($message);
            }

            throw $e;
        }
    }

    /**
     * @param array $orderBy
     * @param int $offset
     * @param int $limit
     * @return EntityInterface[]
     */
    public function findAll(array $orderBy = [], int $offset = 0, int $limit = 0): array
    {
        $query = $this->createQueryBuilder('t')
            ->setFirstResult($offset)
        ;

        if (!empty($orderBy)) {
            $query->orderBy('t.' . $orderBy[0], $orderBy[1]);
        }

        if ($limit !== 0) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }
}
