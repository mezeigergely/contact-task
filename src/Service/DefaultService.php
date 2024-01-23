<?php

namespace App\Service;

class DefaultService
{
    public function existingAdmin($queryBuilder, $adminEntity, $id)
    {
        return $queryBuilder
        ->where('a.username = :username')
        ->andWhere('a.id != :id')
        ->setParameter('username', $adminEntity->getUsername())
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();
    }
}