<?php

namespace App\Service;

use App\Entity\Order as OrdertEntity;
use App\Entity\OrderProduct;
use App\Repository\BasketRepository;
use App\Repository\OrderProductRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use DateInterval;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Flex\Response;

class Order
{

    private $orderRepository;
    private $basketRepository;
    private $doctrine;
    private $productService;
    private $orderProductRepository;

    private $dayAfterShipping = 'P1D';


    public function __construct(ProductRepository $repository,OrderRepository $oRepository,ManagerRegistry $managerRegistry,BasketRepository $bRepository,Product $product,OrderProductRepository $orderProduct)
    {
        $this->productRepository = $repository;
        $this->orderRepository = $oRepository;
        $this->doctrine = $managerRegistry;
        $this->basketRepository = $bRepository;
        $this->productService =$product;
        $this->orderProductRepository = $orderProduct;
    }



    public function createOrder( $userId,$data): array
    {
        $entityManager = $this->doctrine->getManager();
        $basketProducts =  $this->basketRepository->findBy(['userId'=>$userId]);

        if($basketProducts){
            $totolPrice =0;
            foreach ($basketProducts as $bproduct){
                $product = $this->productRepository->find($bproduct->getId());
                if(!$this->productService->checkProductQuantity($product->getId(),$bproduct->getQuantity())){
                    return ['data'=>[],false,sprintf('No quantity Product Name : %s',$product->getName())];
                }
                $totolPrice += $product->getPrice()*$bproduct->getQuantity();
            }

            $order = new OrdertEntity();
            $order->setUserId($userId);
            $order->setAddress($data->get('address'));
            $latestOrder = $this->orderRepository->findBy(array(),array('id'=>'DESC'),1,0);
            $latestOrder = isset($latestOrder[0])? $latestOrder[0]->getId() + 1: 1;
            $order->setOrderCode('#'.str_pad($latestOrder, 8, "0", STR_PAD_LEFT));
            $sippingDate = new \DateTime();
            $sippingDate->add(new DateInterval($this->dayAfterShipping));
            $order->setShippingDate($sippingDate);
            $order->setTotalPrice($totolPrice);
            $entityManager->persist($order);
            $entityManager->flush();

            foreach ($basketProducts as $bproduct){
                $product = $this->productRepository->find($bproduct->getId());
                $product->setQuantity($product->getQuantity() - $bproduct->getQuantity());
                $entityManager->flush();

                $orderProduct = new OrderProduct();
                $orderProduct->setQuantity($bproduct->getQuantity());
                $orderProduct->setProduct($product);
                $orderProduct->setOrder($order);
                $orderProduct->setProductName($product->getName());
                $orderProduct->setPrice($product->getPrice());
                $entityManager->persist($orderProduct);
                $entityManager->flush();

                $entityManager->remove($bproduct);
                $entityManager->flush();
            }


            return [array(),true,'Successful'];
        }else{
            return [array(),'status'=>false, 'Basket Empty'];

        }


    }

    public function getOrders($userId):array
    {
        $data =  $this->orderRepository->findBy(['userId'=>$userId]);
        $orders = [];
        foreach ($data as $order){
            $orderProducts = [];
            foreach ($order->getOrderProducts() as $product){
                $orderProducts[] = [
                    'product_id'=>$product->getproduct()->getId(),
                    'name'=>$product->getProductName(),
                    'price'=>$product->getPrice(),
                    'quantity'=>$product->getQuantity()
                ];
            }
            $orders[] = [
                'orderId'=>$order->getId(),
                'totalPrice'=>$order->getTotalPrice(),
              'products' =>$orderProducts,
              'orderCode' => $order->getOrderCode(),
              'address' => $order->getAddress(),
              'shippingDate'=>$order->getShippingDate()
            ];
        }
        return $orders;
    }

    public function getOrder($userId,$id)
    {
        $data =  $this->orderRepository->find($id);
        if($data->getUserId() !== $userId){
            return [array(),false ,'Not fount Order'];
        }
        $orderProducts = [];
        foreach ($data->getOrderProducts() as $product){
            $orderProducts[] = [
                'product_id'=>$product->getproduct()->getId(),
                'name'=>$product->getProductName(),
                'price'=>$product->getPrice(),
                'quantity'=>$product->getQuantity()
            ];
        }
        $order = [
            'orderId'=>$data->getId(),
            'products' =>$orderProducts,
            'totalPrice'=>$data->getTotalPrice(),
            'orderCode' => $data->getOrderCode(),
            'address' => $data->getAddress(),
            'shippingDate'=>$data->getShippingDate()
        ];
        return [$order,true];
    }

    public function addProductToOrder($userId,$productId,$orderId)
    {
        $entityManager = $this->doctrine->getManager();
        $order =  $this->orderRepository->findBy(['id'=>$orderId,'userId'=>$userId]);
        if(empty($order)){
            return [[],false,'order not found'];
        }
        $now = new \DateTime();
        if($order[0]->getShippingDate()< $now){
            return [[],false,'the order is in the shipping stage'];
        }
        if(!$this->productService->checkProductQuantity($productId,1)){
            return ['data'=>[],false,'No quantity '];
        }

        $orderProducts = $this->orderProductRepository->findBy(['product'=>$productId,'order'=>$orderId]);
        $product = $this->productRepository->find($productId);

        if($orderProducts){
            $orderProducts[0]->setQuantity($orderProducts[0]->getQuantity() + 1);


        }else{
            $orderProduct = new OrderProduct();
            $orderProduct->setQuantity(1);
            $orderProduct->setProduct($product);
            $orderProduct->setOrder($order[0]);
            $orderProduct->setProductName($product->getName());
            $orderProduct->setPrice($product->getPrice());
            $entityManager->persist($orderProduct);

        }
        $product->setQuantity($product->getQuantity() - 1);
        $order[0]->setTotalPrice($order[0]->getTotalPrice()+$product->getPrice());
        $sippingDate = new \DateTime();
        $sippingDate->add(new DateInterval($this->dayAfterShipping));
        $order[0]->setShippingDate($sippingDate);
        $entityManager->flush();
        return [$this->getOrder($userId,$orderId),'status'=>true, 'Successfully'];
    }

    public function removeProductToOrder($userId,$productId,$orderId)
    {
        $entityManager = $this->doctrine->getManager();
        $order =  $this->orderRepository->findBy(['id'=>$orderId,'userId'=>$userId]);
        if(empty($order)){
            return [[],false,'order not found'];
        }
        $now = new \DateTime();
        if($order[0]->getShippingDate()< $now){
            return [[],false,'the order is in the shipping stage'];
        }

        $orderProducts = $this->orderProductRepository->findBy(['product'=>$productId,'order'=>$orderId]);
        $product = $this->productRepository->find($productId);
        if($orderProducts){
            $orderProducts[0]->setQuantity($orderProducts[0]->getQuantity() - 1);

        }else{
            return [[],false,'This product is not in the order'];
        }
        $product->setQuantity($product->getQuantity() + 1);
        $order[0]->setTotalPrice($order[0]->getTotalPrice()-$product->getPrice());
        $sippingDate = new \DateTime();
        $sippingDate->add(new DateInterval($this->dayAfterShipping));
        $order[0]->setShippingDate($sippingDate);
        $entityManager->flush();
        return [$this->getOrder($userId,$orderId),'status'=>true, 'Successfully'];
    }

    public function updateAddress($userId,$address,$orderId)
    {
        $entityManager = $this->doctrine->getManager();
        $order =  $this->orderRepository->findBy(['id'=>$orderId,'userId'=>$userId]);
        if(empty($order)){
            return [[],false,'order not found'];
        }
        $now = new \DateTime();
        if($order[0]->getShippingDate()< $now){
            return [[],false,'the order is in the shipping stage'];
        }
        $order[0]->setAddress($address);
        $sippingDate = new \DateTime();
        $sippingDate->add(new DateInterval($this->dayAfterShipping));
        $order[0]->setShippingDate($sippingDate);
        $entityManager->flush();
        return [$this->getOrder($userId,$orderId),'status'=>true, 'Successfully'];
    }

}