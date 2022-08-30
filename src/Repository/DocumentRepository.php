<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @extends ServiceEntityRepository<Document>
 *
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, ParameterBagInterface $parameterBag)
    {
        parent::__construct($registry, Document::class);
    }

    public function add(Document $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->execute();
        }
    }

    public function remove(Document $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->execute();
        }
    }

    public function execute(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @return Document[] Returns an array of Document objects
     */
    public function findAllOwnerWithShared(User $user): array
    {
        return $this->createQueryBuilder('d')
            ->select('DISTINCT d.safeName, d.name, d.extension, ds.access, o.id as owner ')
            ->innerJoin('d.owner', 'o')
            ->leftJoin('d.documentsShared', 'ds')
            ->leftJoin('ds.receivers', 'r')
            ->where('r.id = :receiverId')
            ->orWhere('o.id = :ownerId')
            ->setParameter('receiverId', $user->getId())
            ->setParameter('ownerId', $user->getId())
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
