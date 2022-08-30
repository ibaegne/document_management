<?php

namespace App\Repository;

use App\Entity\DocumentShared;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findReceiver(string $value, string $fileName, int $id): array
    {
        $subquery = $this->getEntityManager()->createQueryBuilder()
            ->select('r.id')
            ->from(DocumentShared::class, 'ds')
            ->innerjoin('ds.receivers','r')
            ->innerjoin('ds.document','d')
            ->where('d.safeName = :safeName')
            ->getDQL();

        $query = $this->createQueryBuilder('u');
        return $query
            ->select('u.name, u.email')
            ->where('u.isVerified = 1')
            ->andWhere('u.isEnabled  = 1')
            ->andWhere('u.name LIKE :name')
            ->andWhere('u.id != :id')
            ->andWhere(
                $query->expr()->notIn('u.id', $subquery)
            )
            ->setParameter('name', '%' . $value . '%')
            ->setParameter('safeName',  $fileName)
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
