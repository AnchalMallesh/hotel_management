<?php

// CustomerService.php

namespace App\Service;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerService
{
    private $entityManager;
    private $customerRepository;

    public function __construct(EntityManagerInterface $entityManager, CustomerRepository $customerRepository)
    {
        $this->entityManager = $entityManager;
        $this->customerRepository = $customerRepository;
    }

    public function getCustomerById(int $id): JsonResponse
    {
        $customer = $this->customerRepository->find($id);

        if (!$customer) {
            return new JsonResponse(['message' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($customer, Response::HTTP_OK);
    }

    public function createCustomer(array $data): JsonResponse
    {
        $customer = new Customer();
        $customer->setName($data['name']);
        $customer->setEmail($data['email']);

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Customer created', 'id' => $customer->getId()], Response::HTTP_CREATED);
    }

    public function updateCustomer(int $id, array $data): JsonResponse
    {
        $customer = $this->customerRepository->find($id);

        if (!$customer) {
            return new JsonResponse(['message' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $customer->setName($data['name']);
        $customer->setEmail($data['email']);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Customer updated'], Response::HTTP_OK);
    }

    public function deleteCustomer(int $id): JsonResponse
    {
        $customer = $this->customerRepository->find($id);

        if (!$customer) {
            return new JsonResponse(['message' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($customer);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Customer deleted'], Response::HTTP_OK);
    }

    public function getPaginatedCustomers(int $page, int $limit): array
    {
        return $this->customerRepository->findBy([], null, $limit, ($page - 1) * $limit);
    }

    public function getTotalCustomers(): int
    {
        return $this->customerRepository->count([]);
    }
}

