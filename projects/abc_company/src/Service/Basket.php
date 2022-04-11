<?php

namespace App\Service;

use App\Entity\Basket as BasketEnttity;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Flex\Response;

class Basket
{

    private $productRepository;
    private $basketRepository;
    private $doctrine;
    private $productService;


    public function __construct(ProductRepository $repository,BasketRepository $bRepository,ManagerRegistry $managerRegistry, Product $product)
    {
        $this->productRepository = $repository;
        $this->basketRepository = $bRepository;
        $this->doctrine = $managerRegistry;
        $this->productService =$product;
    }



    public function addProduct($data, $userId): array
    {
        $entityManager = $this->doctrine->getManager();
        $basketProducts =  $this->basketRepository->findBy(['userId'=>$userId,'productId'=>$data->get('productId')]);
        if(!$this->productService->checkProductQuantity($basketProducts[0]->getId(),$basketProducts[0]->getQuantity())){
            return ['data'=>[],false,'No quantity '];
        }
        if($basketProducts){
            $quantity = $basketProducts[0]->getQuantity() + $data->get('quantity');
            $basketProducts[0]->setQuantity($quantity);
            $entityManager->flush();
        }else{
            $basket = new BasketEnttity();
            $product =  $this->productRepository->find($data->get('productId'));
            $basket->setProductId($product);
            $basket->setUserId($userId);
            $basket->setQuantity($data->get('quantity'));
            $entityManager->persist($basket);
            $entityManager->flush();
        }
        return ['status'=>true, 'basket'=>$this->getBasket($userId)];

    }

    public function getBasket($userId):array
    {
        $basketProducts =  $this->basketRepository->findBy(['userId'=>$userId]);
        $products = [];
        foreach ($basketProducts as $product){
            $products[] = [
              'name' =>$product->getProductId()->getName(),
              'price' => $product->getQuantity()*$product->getProductId()->getPrice(),
              'quantity' => $product->getQuantity()
            ];
        }
        return $products;
    }
}