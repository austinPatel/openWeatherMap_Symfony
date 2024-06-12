<?php

namespace App\Repository;

use App\Entity\Weather;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Weather>
 *
 * @method Weather|null find($id, $lockMode = null, $lockVersion = null)
 * @method Weather|null findOneBy(array $criteria, array $orderBy = null)
 * @method Weather[]    findAll()
 * @method Weather[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
 class WeatherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Weather::class);
    }

    public function save(Weather $weather): void
    {        
        $this->getEntityManager()->persist($weather);
        $this->getEntityManager()->flush();
    }

    public function fetchWeatherBy(array $criteria){

        $queryBuilder = $this->createQueryBuilder('w');
        if ($criteria['city']) {
            $queryBuilder->andWhere('w.city = :city')
                            ->setParameter('city', $criteria['city']);
        }

        if ($criteria['fetchedAt']) {
            $queryBuilder->andWhere('DATE(w.fetchedAt) = :fetchedAt')
                            ->setParameter('date', $criteria['fetchedAt']->format('Y-m-d'));
        }
        $weatherData = $queryBuilder->getQuery()->getResult();
        return $weatherData;
    }

}
