<?php

namespace App\Controller;

use App\Repository\ProductRepository;

use App\Service\Basket;
use App\Service\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


#[Route('/api', name: 'api_')]
class BasketController extends ApiController
{
    private $basketService;

    public function __construct(Basket $basket)
    {
        $this->basketService = $basket;
    }

    #[Route('/basket', name: 'basket', methods: ['GET'])]
    public function index(UserInterface $user): Response
    {

        return $this->respondWithSuccess($this->basketService->getBasket($user->getId()),true);


    }
    #[Route('/basket', name: 'basket_add_product', methods: ['POST'])]
    public function createProduct(Request $request,UserInterface $user): Response
    {
        $request = $this->transformJsonBody($request);
        $request->userId =$user->getId();

        $data = $this->basketService->addProduct($request,$user->getId());
        if(!$data['status']){
            return $this->respondValidationError($data['errors']);
        }
        return $this->respondWithSuccess($data['basket'],true);
    }


}
