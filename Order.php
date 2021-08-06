<?php


class Order
{
    private Person $customer;

    /**
     * @var array
     */
    private array $products;
    private bool $fastDelivery;
    private array $orderlist;


    public function __construct(Person $person, array $products, bool $fastDelivery, array $orderlist)
    {
        $this->customer=$person;
        $this->products=$products;
        $this->fastDelivery=$fastDelivery;
        $this->orderlist = $orderlist;

    }
    public function getProducts(): array
    {
        return $this->products;
    }

    public function setProducts($products) :array
    {
        $this->products = $products;
    }

    public function calculateTotalPrice()
    {

        $totalPrice = 0;
        foreach($this->products as $productNumber => $product) {
            $totalPrice += $this->orderlist[$productNumber]['price'];
        }
        if ($this->fastDelivery){
            $totalPrice +=2;
        }
        return $totalPrice;

    }

    public function getFormatedPrice()
    {
        $formatedPrice = number_format($this->calculateTotalPrice(), 2);
        return $formatedPrice;

    }
    public function getOrderedItems(){
        $orderedItems=[];
        foreach($this->products as $productNumber => $product) {
            array_push($orderedItems, $this->orderlist[$productNumber]['name']);
        }
        return$orderedItems;
    }

    public function getDeliveryTime()
    {
        $deliveryTime = date('d/m/Y h:i a', time()+ (7 * 24 * 60 * 60));
        if ($this->fastDelivery){
            $deliveryTime = date('d/m/Y h:i a', time()+ (2 * 24 * 60 * 60));
        }
        return $deliveryTime;
    }
}
