<?php

namespace App\Service;
use App\Entity\Product as ProductEntity;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Flex\Response;

class Product
{

    private $productRepository;
    private $doctrine;
    private $validator;

    public function __construct(ProductRepository $repository,ValidatorInterface $validatorInterface,ManagerRegistry $managerRegistry)
    {
        $this->productRepository = $repository;
        $this->doctrine = $managerRegistry;
        $this->validator = $validatorInterface;
    }

    public function GetProducts(): array
    {
        $data = $this->productRepository->findAll();
        $products = [];
        foreach ($data as $product){
            $products[] = [
                'id'=>$product->getId(),
                'name'=>$product->getName(),
                'quantity'=>$product->getQuantity(),
                'price'=>$product->getPrice()
            ];
        }

        return $products;
    }

    public function createProduct($data): array
    {

        $entityManager = $this->doctrine->getManager();
        $product = new ProductEntity();
        $product->setName($data->get('name'));
        $product->setPrice($data->get('price'));
        $product->setQuantity($data->get('quantity'));

        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            foreach ($errors as $error){
                $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
            }
            return ['status'=>false,'errors'=> $errorsArray];
        }

        $entityManager->persist($product);
        $entityManager->flush();
        return ['status'=>true, 'product'=>$this->getProduct($product->getId())];

    }

    public function getProduct($id):array
    {
        $product =  $this->productRepository->find($id);
        if($product){
            return [
                'id'=>$product->getId(),
                'name'=>$product->getName(),
                'quantity'=>$product->getQuantity(),
                'price'=>$product->getPrice(),

            ];
        }
        return [];

    }
    public function checkProductQuantity($productId,$quantity) :bool
    {
        $product = $this->productRepository->find(['id'=>$productId]);
        if(!$product){
            return  false;
        }
        return $product->getQuantity() >= $quantity;
    }
}