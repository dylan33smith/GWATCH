<?php

namespace App\Repository;

use App\Entity\Genes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Genes>
 *
 * @method Genes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genes[]    findAll()
 * @method Genes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genes::class);
    }

    public function save(Genes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Genes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find genes by chromosome
     */
    public function findByChromosome(int $chr): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.chr = :chr')
            ->setParameter('chr', $chr)
            ->orderBy('g.posstart', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find genes by chromosome and position range
     */
    public function findByChromosomeAndPosition(int $chr, int $start, int $end): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.chr = :chr')
            ->andWhere('g.posstart <= :end')
            ->andWhere('g.posend >= :start')
            ->setParameter('chr', $chr)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('g.posstart', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

