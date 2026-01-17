<?php
declare(strict_types=1);

namespace App\Controller\V1;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('countries', name: 'countries')]
class CountryController extends ApiController
{
    /**
     * @param  EntityManagerInterface  $entityManager
     * @param  SerializerInterface  $serializer
     * @param  CountryRepository  $countryRepository
     */
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SerializerInterface $serializer,
        protected CountryRepository $countryRepository,
    ) {
        //
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getCountries(Request $request): Response
    {
        $query = $this->entityManager->getRepository(Country::class)->createQueryBuilder('q');

        $countries = $this->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10),
            $this->serializer
        );

        return $this->apiPagination(data: $countries);
    }

    #[Route('/{country}', methods: ['GET'])]
    public function getCountry(string $country)
    {
        $data = $this->entityManager->getRepository(Country::class)->find($country);

        if (!$data) {
            return $this->apiResponse(message: 'Country not found.', status: Response::HTTP_NOT_FOUND);
        }

        return $this->apiResponse(message: 'Country created successfully', data: $this->serializer->normalize($data));
    }

    /**
     * Store a new country.
     *
     * @OA\RequestBody(
     *     description="Country data",
     *     required=true,
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="code", type="string", example="USA"),
     *         @OA\Property(property="name", type="string", example="United States"),
     *         @OA\Property(property="region", type="string", example="Americas"),
     *         @OA\Property(property="subregion", type="string", example="Northern America"),
     *         @OA\Property(property="population", type="integer", example=331002651),
     *         @OA\Property(property="flag", type="string", example="https://restcountries.com/v3.1/flags/usa.svg")
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Country created",
     *     @OA\JsonContent(
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="code", type="string", example="USA"),
     *         @OA\Property(property="name", type="string", example="United States"),
     *         @OA\Property(property="region", type="string", example="Americas"),
     *         @OA\Property(property="subregion", type="string", example="Northern America"),
     *         @OA\Property(property="population", type="integer", example=331002651),
     *         @OA\Property(property="flag", type="string", example="https://restcountries.com/v3.1/flags/usa.svg")
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid input"
     * )
     * @OA\Tag(name="Country")
     * @throws JsonException
     */
    #[Route('', methods: ['POST'])]
    public function addCountry(Request $request, ValidatorInterface $validator)
    {
        $data = $request->getContent()
            ? json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR)
            : [];

        $violations = $this->getConstraintViolationList($validator, $data);

        // If validation fails, return a custom response
        if (count($violations) > 0) {
            return $this->apiValidationError(violations: $violations);
        }

        // Save country
        $country = $this->countryRepository->create($data);

        $this->entityManager->persist($country);
        $this->entityManager->flush();

        // Serialize the Country object to JSON
        $data = $this->serializer->normalize($country);

        return $this->apiResponse(message: 'Country created successfully', data: $data);
    }

    /**
     * @throws JsonException
     */
    #[Route('/{country}', methods: ['PATCH'])]
    public function updateCountry(Request $request, string $country, ValidatorInterface $validator)
    {
        $countryObject = $this->entityManager->getRepository(Country::class)->find($country);

        if (!$countryObject) {
            return $this->apiResponse(message: 'Country not found.', status: Response::HTTP_NOT_FOUND);
        }

        $data = $request->getContent()
            ? json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR)
            : [];

        $violations = $this->getConstraintViolationList($validator, $data);

        // If validation fails, return a custom response
        if (count($violations) > 0) {
            return $this->apiValidationError(violations: $violations);
        }

        // update country
        $this->countryRepository->update($countryObject, $data);

        $this->entityManager->flush();

        // Serialize the Country object to JSON
        $data = $this->serializer->normalize($data);

        return $this->apiResponse(message: 'Country updated successfully', data: $data);
    }

    #[Route('/{country}', methods: ['DELETE'])]
    public function deleteCountry(string $country)
    {
        $data = $this->entityManager->getRepository(Country::class)->find($country);

        if (!$data) {
            return $this->apiResponse(message: 'Country not found.', status: Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->remove($data);
        $this->entityManager->flush();

        return $this->apiResponse(message: 'Country deleted successfully!', status: Response::HTTP_OK);
    }

    /**
     * @param  ValidatorInterface  $validator
     * @param  array  $request
     * @return ConstraintViolationListInterface
     */
    public function getConstraintViolationList(
        ValidatorInterface $validator,
        array $request
    ): ConstraintViolationListInterface {

        // Define validation constraints
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Length(['min' => 3])],
            'region' => [new Assert\NotBlank(), new Assert\Length(['min' => 3])],
            'subRegion' => [new Assert\NotBlank(), new Assert\Length(['min' => 3])],
            'demonym' => [new Assert\NotBlank(), new Assert\Length(['min' => 3])],
            'population' => [new Assert\NotBlank(), new Assert\Type('integer')],
            'independent' => [new Assert\NotBlank(), new Assert\Type('boolean')],
            'flag' => [new Assert\NotBlank(), new Assert\Length(['min' => 2, 'max' => 8])],
            'currencyName' => [new Assert\NotBlank(), new Assert\Length(['min' => 2])],
            'currencySymbol' => [new Assert\NotBlank(), new Assert\Length(['min' => 1])],
        ]);


        // Validate the data against constraints
        return $validator->validate($request, $constraints);
    }
}