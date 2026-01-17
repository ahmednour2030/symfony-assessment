<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CountryRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * @param $data
     * @return Country
     */
    public function create($data): Country
    {
        $country = new Country();

        return $this->dataObject($country, $data);
    }

    /**
     * @param $data
     * @param $request
     * @return Country
     */
    public function update($data, $request): Country
    {
        return $this->dataObject($data, $request);
    }

    /**
     * @param $data
     * @param $request
     * @return mixed
     */
    protected function dataObject($data, $request): mixed
    {
        $data->setName($request['name']);
        $data->setRegion($request['region']);
        $data->setSubRegion($request['subRegion']);
        $data->setDemonym($request['demonym']);
        $data->setPopulation($request['population']);
        $data->setIndependent($request['independent']);
        $data->setFlag($request['flag']);
        $data->setCurrencyName($request['currencyName']);
        $data->setCurrencySymbol($request['currencySymbol']);

        return $data;
    }

}