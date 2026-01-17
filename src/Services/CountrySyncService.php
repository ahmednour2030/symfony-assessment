<?php

namespace App\Services;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CountrySyncService
{
    /**
     * @param  HttpClientInterface $client
     * @param  EntityManagerInterface $em
     */
    public function __construct(protected HttpClientInterface $client, protected EntityManagerInterface $em)
    {
        //
    }

    public function syncCountries(int $batchSize = 30): void
    {
        // Fetch country data from the external API
        $response = $this->client->request('GET', 'https://restcountries.com/v3.1/all');
        $countriesData = $response->toArray();

        $countryBatches = array_chunk($countriesData, $batchSize); // Split the countries into batches

        foreach ($countryBatches as $batch) {
            $countryFlags = array_column($batch, 'cca2'); // Get the list of country codes for this batch

            // Use a query to fetch the existing countries in the current batch
            $existingCountries = $this->em->getRepository(Country::class)
                ->createQueryBuilder('c')
                ->where('c.flag IN (:flags)')
                ->setParameter('flags', $countryFlags)
                ->getQuery()
                ->getResult();

            // Create a map for existing countries by code
            $existingCountryMap = [];
            foreach ($existingCountries as $existingCountry) {
                $existingCountryMap[$existingCountry->getFlag()] = $existingCountry;
            }

            foreach ($batch as $countryData) {
                $countryFlag = $countryData['cca2'];

                // Check if the country exists in the batch fetched from the database
                if (isset($existingCountryMap[$countryFlag])) {
                    $country = $existingCountryMap[$countryFlag];
                } else {
                    $country = new Country();
                    $country->setFlag((string)$countryFlag);
                }

                // Update or set country details
                $country->setName($countryData['name']['common']);
                $country->setRegion($countryData['region']);
                $country->setSubregion($countryData['subregion'] ?? 'not found');
                $country->setPopulation($countryData['population']);
                $country->setIndependent($countryData['independent'] ?? false);
                $country->setDemonym($countryData['name']['official']);

                $country->setCurrencyName(
                    isset($countryData['currencies'])
                        ?array_values($countryData['currencies'])[0]['name']
                        : 'not found'
                );

                $country->setCurrencySymbol(
                    isset($countryData['currencies'])
                        ? array_values($countryData['currencies'])[0]['symbol']
                        : 'not found'
                );

                // Persist the country
                $this->em->persist($country);
            }

            // Flush and clear the batch
            $this->em->flush();
            $this->em->clear();
        }

        $countryFlags = array_column($countriesData, 'cca2');

        //  removes countries that do not exist according
        $qb = $this->em->createQueryBuilder();

        $qb->delete(Country::class, 'c')
            ->where('c.flag NOT IN (:flags)')
            ->setParameter('flags', $countryFlags)
            ->getQuery()
            ->execute();
    }
}
