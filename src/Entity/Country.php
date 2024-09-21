<?php
declare(strict_types=1);

namespace App\Entity;
use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @Table(name="countries", indexes={
 *     @Index(name="idx_id", columns={"id"}),
 *     @Index(name="idx_flag", columns={"flag"})
 * })
 */
#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private ?string $id;

    #[ORM\Column(type: 'string', length: 200)]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 200)]
    private ?string $region;

    #[ORM\Column(type: 'string', length: 200)]
    private ?string $subregion;

    #[ORM\Column(type: 'string', length: 200)]
    private ?string $demonym;

    #[ORM\Column(type: 'integer')]
    private int $population;

    #[ORM\Column(type: 'boolean')]
    private ?bool $independent;

    #[ORM\Column(type: 'string', length: 8)]
    private ?string $flag;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $currencyName;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $currencySymbol;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122(); // Automatically assign a UUID
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getSubRegion(): string
    {
        return $this->subregion;
    }

    /**
     * @param string $subregion
     */
    public function setSubRegion(string $subregion): void
    {
        $this->subregion = $subregion;
    }

    /**
     * @return string
     */
    public function getDemonym(): string
    {
        return $this->demonym;
    }

    /**
     * @param string $demonym
     */
    public function setDemonym(string $demonym): void
    {
        $this->demonym = $demonym;
    }

    /**
     * @return int
     */
    public function getPopulation(): int
    {
        return $this->population;
    }

    /**
     * @param int $population
     */
    public function setPopulation(int $population): void
    {
        $this->population = $population;
    }

    /**
     * @return bool
     */
    public function getIndependent(): bool
    {
        return $this->independent;
    }

    /**
     * @param bool $independent
     */
    public function setIndependent(bool $independent): void
    {
        $this->independent = $independent;
    }

    /**
     * @return string
     */
    public function getFlag(): string
    {
        return $this->flag;
    }

    /**
     * @param string $flag
     */
    public function setFlag(string $flag): void
    {
        $this->flag = $flag;
    }

    /**
     * @return string
     */
    public function getCurrencyName(): string
    {
        return $this->currencyName;
    }

    /**
     * @param  string  $currencyName
     */
    public function setCurrencyName(string $currencyName): void
    {
        $this->currencyName = $currencyName;
    }

    /**
     * @return string
     */
    public function getCurrencySymbol(): string
    {
        return $this->currencySymbol;
    }

    /**
     * @param  string  $currencySymbol
     */
    public function setCurrencySymbol(string $currencySymbol): void
    {
        $this->currencySymbol = $currencySymbol;
    }

}