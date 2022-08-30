<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\DocumentShared;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentShared>
 *
 * @method DocumentShared|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentShared|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentShared[]    findAll()
 * @method DocumentShared[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentSharedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentShared::class);
    }

    public function add(DocumentShared $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->execute();
        }
    }

    public function remove(DocumentShared $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->execute();
        }
    }

    public function findOneByDocumentAndReceiver(Document $document, User $user): ?DocumentShared
    {
        return $this->createQueryBuilder('ds')
            ->innerJoin('ds.receivers', 'r')
            ->innerJoin('ds.document', 'd')
            ->where('r.id = :receiverId')
            ->andWhere('d.id = :documentId')
            ->setParameter('receiverId', $user->getId())
            ->setParameter('documentId', $document->getId())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllReceiver(User $user): array
    {
        return $this->createQueryBuilder('ds')
            ->select('DISTINCT d.safeName, d.name, d.extension, ds.access, o.id as owner ')
            ->innerJoin('ds.receivers', 'r')
            ->innerJoin('ds.document', 'd')
            ->innerJoin('d.owner', 'o')
            ->where('r.id = :id')
            ->setParameter('id', $user->getId())
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getArrayResult()
            ;
    }

    public function execute(): void
    {
        $this->getEntityManager()->flush();
    }
}
