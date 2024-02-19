<?php

namespace App\Controller;

use App\DTO\CreateCustomerRequest;
use App\Service\CustomerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/customers")
 */
class CustomerController extends AbstractController
{
    private $customerService;
    private $serializer;
    private $validator;

    public function __construct(CustomerService $customerService, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->customerService = $customerService;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/api/customers", name="get_customers", methods={"GET"})
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $paginator = $this->customerService->getPaginatedCustomers($page, $limit);
        $totalCustomers = $this->customerService->getTotalCustomers();

        $customerDtos = array_map(function ($customer) {
            return $this->serializeCustomer($customer);
        }, $paginator);

        $prevPage = ($page > 1) ? $page - 1 : null;
        $nextPage = ($page * $limit < $totalCustomers) ? $page + 1 : null;

        $responseData = [
            'data' => $customerDtos,
            'pagination' => [
                'total' => $totalCustomers,
                'page' => $page,
                'limit' => $limit,
                'prev_page' => $prevPage,
                'next_page' => $nextPage,
            ],
        ];

        return new JsonResponse($responseData, Response::HTTP_OK);
    }

    /**
     * @Route("/api/customers/{id}", name="get_customer", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        return $this->customerService->getCustomerById($id);
    }

    /**
     * @Route("/api/customers", name="create_customer", methods={"POST"})
     */
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $createCustomerRequest = $this->serializer->deserialize($request->getContent(), CreateCustomerRequest::class, 'json');

        // Validate the request
        $errors = $this->validator->validate($createCustomerRequest);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        return $this->customerService->createCustomer($createCustomerRequest);
    }

    /**
     * @Route("/api/customers/{id}", name="update_customer", methods={"PUT"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        return $this->customerService->updateCustomer($id, $data);
    }

    /**
     * @Route("/api/customers/{id}}", name="delete_customer", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        return $this->customerService->deleteCustomer($id);
    }

    private function serializeCustomer($customer): array
    {
        return [
            'id' => $customer->getId(),
            'name' => $customer->getName(),
            'email' => $customer->getEmail(),
        ];
    }
}
