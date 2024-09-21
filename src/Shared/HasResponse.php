<?php

namespace App\Shared;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait HasResponse
{
    /**
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  array  $context
     * @return JsonResponse
     */
    protected function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @param  string  $message
     * @param  $data
     * @param  int  $status
     * @return mixed
     */
    public function apiResponse(
        string $message = 'success',
        $data = null,
        int $status = Response::HTTP_CREATED): mixed
    {
        return $this->json(['message' => $message, 'data' => $data], $status);
    }

    /**
     * @param  string  $message
     * @param  $data
     * @param  int  $status
     * @return mixed
     */
    public function apiPagination(
        $data,
        string $message = 'success',
        int $status = Response::HTTP_OK): mixed
    {
        return $this->json([
            'message' => $message,
            'data' => [
                'items' => $data?->getItems() ?? [],
                'current_page' => $data?->getCurrentPage(),
                'last_page' => $data?->getLastPage(),
                'total' => $data?->getTotal(),
            ]
        ], $status);
    }

    /**
     * @param  ConstraintViolationListInterface  $violations
     * @param  string  $message
     * @param  int  $status
     * @return JsonResponse
     */
    public function apiValidationError(
        ConstraintViolationListInterface $violations,
        string $message = 'Error validation',
        int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $errors = [];

        foreach ($violations as $violation) {
            $errors[preg_replace('/[\[\]]/', '', $violation->getPropertyPath())] = $violation->getMessage();
        }

        return $this->json(['message' => $message, 'errors' => $errors], $status);
    }
}