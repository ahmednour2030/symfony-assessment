<?php

namespace App\Shared;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as OrmPaginator;
use Symfony\Component\Serializer\SerializerInterface;

trait HasPagination
{
    /**
     * @var integer
     */
    private int $total;

    /**
     * @var integer
     */
    private int $lastPage;
    /**
     * @var integer
     */
    private int $currentPage;

    private $items;

    /**
     * @param  Query|DoctrineQueryBuilder  $query
     * @param  int  $page
     * @param  int  $limit
     * @param $serializer
     * @return $this
     */
    public function paginate(Query|DoctrineQueryBuilder $query, int $page = 1, int $limit = 10, $serializer = NULL): static
    {
        $paginator = new OrmPaginator($query, true);


        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        $this->total = $paginator->count();
        $this->currentPage = $page;
        $this->lastPage = (int) ceil($paginator->count() / $paginator->getQuery()->getMaxResults());
        $this->items = $serializer ? $serializer->normalize($paginator): $paginator;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getItems()
    {
        return $this->items;
    }
}