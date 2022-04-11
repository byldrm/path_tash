<?php

namespace App\Controller;

use App\Repository\ProductRepository;

use App\Service\Basket;
use App\Service\Order;
use App\Service\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


#[Route('/api', name: 'api_')]
class OrderController extends ApiController
{
    private $orderService;

    public function __construct(Order $order)
    {
        $this->orderService = $order;
    }

    #[Route('/orders', name: 'orders', methods: ['GET'])]
    public function index(UserInterface $user): Response
    {

        return $this->respondWithSuccess($this->orderService->getOrders($user->getId()),true);


    }

    #[Route('/orders', name: 'create_order', methods: ['POST'])]
    public function createOrders(UserInterface $user,Request $request): Response
    {
        $request = $this->transformJsonBody($request);

        return $this->respondWithSuccess($this->orderService->createOrder($user->getId(),$request));
    }

    #[Route('/orders/{id}', name: 'get_order', methods: ['GET'])]
    public function getOrders(UserInterface $user, $id): Response
    {
        return $this->respondWithSuccess($this->orderService->getOrder($user->getId(),$id));
    }

    #[Route('/orders/add_product', name: 'add_product_to_order', methods: ['POST'])]
    public function addProductToOrder(UserInterface $user, Request $request): Response
    {
        $request = $this->transformJsonBody($request);
        return $this->respondWithSuccess($this->orderService->addProductToOrder($user->getId(),$request->get('productId'),$request->get('orderId')));
    }

    #[Route('/orders/remove_product', name: 'remove_product_to_order', methods: ['POST'])]
    public function removeProductToOrder(UserInterface $user, Request $request): Response
    {
        $request = $this->transformJsonBody($request);
        return $this->respondWithSuccess($this->orderService->removeProductToOrder($user->getId(),$request->get('productId'),$request->get('orderId')));
    }

    #[Route('/orders/update_address', name: 'update_address_to_order', methods: ['POST'])]
    public function updateAddress(UserInterface $user, Request $request): Response
    {
        $request = $this->transformJsonBody($request);

        return $this->respondWithSuccess($this->orderService->updateAddress($user->getId(),$request->get('address'),$request->get('orderId')));
    }




}
