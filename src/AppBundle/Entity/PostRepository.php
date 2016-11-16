<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class PostRepository
 */
class PostRepository extends EntityRepository
{
    public function getAllCountries()
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->select('DISTINCT(p.country)');

        $results   = $queryBuilder->getQuery()->getArrayResult();
        $countries = [];

        foreach ($results as $result) {
            $countries[] = current($result);
        }

        return $countries;
    }

    public function getAllContinents()
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->select('DISTINCT(p.continent)');

        $results   = $queryBuilder->getQuery()->getArrayResult();
        $contients = [];

        foreach ($results as $result) {
            $contients[] = current($result);
        }

        return $contients;
    }
}