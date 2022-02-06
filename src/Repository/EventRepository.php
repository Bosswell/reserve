<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception as DbalException;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeImmutable;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @throws DbalException
     */
    public function countOngoingSubjectEventsInPeriod(DateTimeImmutable $startAt, DateTimeImmutable $endAt, Subject $subject): int
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT count(*) FROM event e
            JOIN subject s ON e.subject_id = s.id
            WHERE s.id = :subjectId 
                AND (
                    (e.start_at <= :startAt AND e.end_at >= :endAt) OR
                    (e.start_at >= :startAt AND e.end_at <= :endAt) OR
                    (e.start_at >= :startAt AND e.end_at >= :endAt AND e.start_at <= :endAt) OR
                    (e.start_at <= :startAt AND e.end_at <= :endAt AND e.start_at >= :endAt)
                )
        ';

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':subjectId', $subject->getId());
        $stmt->bindValue(':startAt', $startAt->format('Y-m-d H:i:s'));
        $stmt->bindValue(':endAt', $endAt->format('Y-m-d H:i:s'));

        return $stmt
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * @return Event[]
     */
    public function findBySubject(Subject $subject, ?DateTimeImmutable $startAt = null, ?DateTimeImmutable $endAt = null, int $limit = 100): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.subject = :subject')
            ->setParameter('subject', $subject)
            ->setMaxResults($limit);

        if ($startAt !== null) {
            $qb->andWhere('e.startAt >= :startAt');
            $qb->setParameter('startAt', $startAt);
        }

        if ($endAt !== null) {
            $qb->andWhere('e.endAt <= :endAt');
            $qb->setParameter('endAt', $endAt);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }
}
