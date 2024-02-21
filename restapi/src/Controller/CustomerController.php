<?php

namespace App\Controller;

use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/api/v1/customers')]
class CustomerController extends AbstractController
{
    private $serializer;
    private $validator;
    private $entityManager;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'get_customers', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 4);
        $offset = ($page - 1) * $limit;
        $customers = $this->entityManager->getRepository(Customer::class)->findBy([], null, $limit, ($page - 1) * $limit);

        $customerData = [];
        foreach ($customers as $customer) {
            $customerData[] = [
                'id' => $customer->getId(),
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
            ];
        }

        $totalCustomers = count($customerData);

        // $prevPage = ($page > 1) ? $page - 1 : null;
        // $nextPage = ($page * $limit < $totalCustomers) ? $page + 1 : null;

        // $responseData = [
        //     'data' => $customerData,
        //     'pagination' => [
        //         'total' => $totalCustomers,
        //         'page' => $page,
        //         'limit' => $limit,
        //         'prev_page' => $prevPage,
        //         'next_page' => $nextPage,
        //     ],
        // ];

        return $this->json($customerData);
        //return new JsonResponse($responseData, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get_customer', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $customer = $this->entityManager->getRepository(Customer::class)->find($id);

        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $customerData = [
            'id' => $customer->getId(),
            'name' => $customer->getName(),
            'email' => $customer->getEmail(),
        ];

        return new JsonResponse($customerData, Response::HTTP_OK);
    }

    #[Route('/', name: 'create_customer', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $customer = new Customer();
        $customer->setName($data['name']);
        $customer->setEmail($data['email']);

        $errors = $this->validator->validate($customer);
        if (count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        $responseData = [
            'id' => $customer->getId(),
            'name' => $customer->getName(),
            'email' => $customer->getEmail(),
        ];

        return new JsonResponse($responseData, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_customer', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $customer = $this->entityManager->getRepository(Customer::class)->find($id);

        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $customer->setName($data['name']);
        $customer->setEmail($data['email']);

        $errors = $this->validator->validate($customer);
        if (count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $this->entityManager->flush();

        $responseData = [
            'id' => $customer->getId(),
            'name' => $customer->getName(),
            'email' => $customer->getEmail(),
        ];

        return new JsonResponse($responseData, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete_customer', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $customer = $this->entityManager->getRepository(Customer::class)->find($id);

        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($customer);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Customer deleted successfully'], Response::HTTP_NO_CONTENT);
    }

    private function validationErrorResponse($errors): JsonResponse
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }
        return new JsonResponse(['error' => $errorMessages], Response::HTTP_BAD_REQUEST);
    }
}
