<?php

namespace Grossum\ExtendedFormTypeBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class DependentEntityLoader
{
    /** @var Registry $doctrine */
    private $doctrine;

    /**
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param array $entityInf
     * @param int $parentId
     * @return array
     */
    public function getEntities($entityInf, $parentId)
    {
        /** @var EntityRepository $repository */
        $repository = $this->doctrine->getRepository($entityInf['class']);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $repository->createQueryBuilder('e');

        $queryBuilder
            ->where($queryBuilder->expr()->eq('e.' . $entityInf['parent_property'], ':parentId'))
            ->orderBy('e.' . $entityInf['order_property'], $entityInf['order_direction'])
            ->setParameter('parentId', $parentId);

        $this->setCallback($repository, $queryBuilder, $entityInf);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param EntityRepository $repository
     * @param QueryBuilder $queryBuilder
     * @param array $entityInf
     */
    private function setCallback(
        EntityRepository $repository,
        QueryBuilder $queryBuilder,
        array $entityInf
    ) {
        if (!isset($entityInf['callback'])) {
            return;
        }
        if (!method_exists($repository, $entityInf['callback'])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Callback function "%s" in Repository "%s" does not exist.',
                    $entityInf['callback'],
                    get_class($repository)
                )
            );
        }
        $repository->$entityInf['callback']($queryBuilder);
    }
}
