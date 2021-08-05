<?php

// This file is your starting point (= since it's the index)
// It will contain most of the logic, to prevent making a messy mix in the html

// This line makes PHP behave in a more strict way
declare(strict_types=1);

//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);

// We are going to use session variables so we need to enable sessions
session_start();

$orderMessage = "";

$totalPrice=0;

if(!empty($_GET)){
    foreach ($_GET as $key=>$get){
        if(!in_array($key, ['orderlist'])){ //to send only allowed keys in $_GET
            unset($_GET[$key]);
        }
    }

    if(!in_array($_GET['orderlist'], ['food', 'drinks'])){  //to send only allowed values in 'order'
        unset($_GET['orderlist']);
    }
}


// Use this function when you need to need an overview of these variables
class Products
{
    public static function food(){
        return $food = [
            ['name' => 'Apple', 'price' => 2.5],
            ['name' => 'Watter melon', 'price' => 2.5],
            ['name' => 'Orange', 'price' => 3],
            ['name' => 'Black Berries', 'price' => 3.5],
            ['name' => 'Pear', 'price' => 4],
            ['name' => 'Lemon', 'price' => 6],
            ['name' => 'Peach', 'price' => 2],
        ];

    }
    public static function drinks(){
        return $drinks = [
            ['name' => 'Fanta', 'price' => 2.5],
            ['name' => 'Coca Cola', 'price' => 2.5],
            ['name' => 'Black tea', 'price' => 3],
            ['name' => 'Coffee', 'price' => 3.5],
            ['name' => 'Iced Coffee', 'price' => 4],
            ['name' => 'Irish Coffee', 'price' => 6],
            ['name' => 'Water', 'price' => 3],
        ];
    }

}
$productslist = new Products();

$orderlist =  $productslist->drinks();
$chosenproducts='drinks';

if($_GET['orderlist'] == 'food'){
    $orderlist= $productslist->food();
    $chosenproducts='food';
}
if($_GET['orderlist'] == 'drinks'){
    $orderlist= $productslist->drinks();
    $chosenproducts='drinks';
}

class Order
{
    private Person $customer;

    /**
     * @var array
     */
    private array $products;
    private bool $fastDelivery;
    private array $orderlist;

    public function __construct($person, $products, $fastDelivery, $orderlist)
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
    public function getOrderedItems(){
        $orderedItems=[];
        foreach($this->products as $productNumber => $product) {
            array_push($orderedItems, $this->orderlist[$productNumber]['name']);
        }
        return$orderedItems;
    }

    public function getDeliveryTime()
    {
        if ($this->fastDelivery){
            $deliveryTime = date('d/m/Y h:i a', time()+ (2 * 24 * 60 * 60));

        } else {
            $deliveryTime = date('d/m/Y h:i a', time()+ (7 * 24 * 60 * 60));
        };
        return $deliveryTime;
    }



}

class Person
{
    private string $email;
    private stdClass $address;

    public function __construct(string $email, stdClass $address)
    {
        $this->email=$email;
        $this->address = new stdClass();
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return stdClass
     */
    public function getAddress(): stdClass
    {
        return $this->address;
    }

    /**
     * @param stdClass $address
     */
    public function setAddress(stdClass $address): void
    {
        $this->address = $address;
    }
}

class Request{
    private array $input;
    private string $email;
    private stdClass $address;
    private array $products;
    private bool $fastDelivery=false;
    private array $errors=[];


    public function __construct($input){
        $this->input=$input;
        $this->address = new stdClass();

    }

    public function validate()
    {

        if (!filter_var($this->input['email'], FILTER_VALIDATE_EMAIL)) {
           array_push($this->errors, 'Please, check your email address.');
        }
        else{
            $this->email = $this->input['email'];
        }

        if ($this->input['street'] == ''){
            array_push($this->errors, 'Street field can not be empty.');
        }
        else{
            $this->address->street=$this->input['street'];
        }
        if ($this->input['city'] == ''){
            array_push($this->errors, 'City field can not be empty.');
        }
        else{
            $this->address->city=$this->input['city'];
        }
        if (($this->input['streetnumber'] == '') || !(is_numeric(($this->input['streetnumber'])))){

            array_push($this->errors, 'Street number field can not be empty and has to be a number.');
        }
        else{
            $this->address->streetnumber=$this->input['streetnumber'];
        }
        if (!(is_numeric(($this->input['zipcode'])))){
            array_push($this->errors, 'Zip code field can not be empty and has to be a number.');
        }
        else{
            $this->address->zipcode=$this->input['zipcode'];
        }

        if (count($this->input['products']) === 0){
            array_push($this->errors, 'Please, chose at least one product.');
        }
        else{
            $this->products=$this->input['products'];

        }
        if(array_key_exists('deliveryTime' ,$this->input)){
            $this->fastDelivery=true;
        }
        else
        {
            $this->fastDelivery=false;
        }

        return !$this->hasErrors();

    }

    public function data(?string $param = null)
    {
        $data = ['email' => $this->email, 'address' => $this->address, 'products' => $this->products, 'fastDelivery' => $this->fastDelivery];
        if(!is_null($param) and in_array($param, array_keys($data))) {
            return $data[$param];
        }

        return $data;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}

if (!empty($_POST)) {
    $request = new Request($_POST);


    if($request->validate()){

        $person = new Person($request->data('email'), $request->data('address'));
        $order = new Order($person, $request->data('products'), $request->data('fastDelivery'), $orderlist);

        $totalPrice=$order->calculateTotalPrice();
        $orderMessage = ' <div class="alert alert-success">
                          Your order is sumbited </br> Your address is: ' .$person->getAddress()->street . ' ' .$person->getAddress()->streetnumber . ' ' . ' ' .$person->getAddress()->city
                          .'</br>Your email is: ' .$person->getEmail()
                        .'</br> You have chosen: ' .implode(" , ", $order->getOrderedItems())
                        .'</br> The total price is: &euro;' .number_format($order->calculateTotalPrice(), 2)
                        .'</br>Estimated delivery time: ' .$order->getDeliveryTime()
                        .'</div>';


    }
    else{
        $errors = $request->errors();
        $orderMessage = '<div class="alert alert-danger">' . implode(" </br> ", $errors ) .'</div>';

    }
}



require 'form-view.php';

