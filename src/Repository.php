<?php

namespace Vinorcola\HelperBundle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

abstract class Repository
{
    /**
     * Repository constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(protected EntityManagerInterface $entityManager) {}

    /**
     * Must return the class of the linked entity.
     *
     * @return string
     */
    abstract public static function getEntityClass(): string;

    /**
     * Return a reference of an entity.
     *
     * @param string $entityClass
     * @param string $id
     * @return object
     */
    protected function getReference(string $entityClass, string $id)
    {
        return $this->entityManager->getReference($entityClass, $id);
    }

    /**
     * Prepare a query builder.
     *
     * @param string $alias
     * @return QueryBuilder
     */
    protected function createQueryBuilder(string $alias): QueryBuilder
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(static::getEntityClass(), $alias)->select($alias);
    }
}
