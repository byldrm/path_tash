<?php

namespace App\Controller;

use App\Repository\ProductRepository;

use App\Service\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api', name: 'api_')]
class ProductController extends ApiController
{
    private $productService;

    public function __construct(Product $product)
    {
        $this->productService = $product;
    }

    #[Route('/products', name: 'products', methods: ['GET'])]
    public function index(): Response
    {
        return $this->respondWithSuccess($this->productService->GetProducts(),true);


    }
 /*   #[Route('/products', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request): Response
    {
        $request = $this->transformJsonBody($request);
        $data = $this->productService->createProduct($request);
        if(!$data['status']){
            return $this->respondValidationError($data['errors']);
        }
        return $this->respondWithSuccess($data['product'],true);
    }*/

    #[Route('/products/{id}', name: 'get_product', methods: ['GET'])]
    public function getProduct($id)
    {
        $product = $this->productService->GetProduct($id);
        if(!$product){
           return $this->respondNotFound();
        }
        return $this->respondWithSuccess($product,true);
    }
}
