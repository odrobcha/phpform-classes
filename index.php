<?php

// This file is your starting point (= since it's the index)
// It will contain most of the logic, to prevent making a messy mix in the html

// This line makes PHP behave in a more strict way
declare(strict_types=1);

// We are going to use session variables so we need to enable sessions
session_start();

$order = $_GET['order'] ?? 'drinks';
$deliveryDate = date('m/d/Y h:i a', time()+ (7 * 24 * 60 * 60));
$deliveryTime = $_POST['deliveryTime'] ?? date('d/m/Y h:i a', time()+ (7 * 24 * 60 * 60));

$drinks = [
    ['name' => 'Fanta', 'price' => 2.5],
    ['name' => 'Coca Cola', 'price' => 2.5],
    ['name' => 'Black tea', 'price' => 3],
    ['name' => 'Coffee', 'price' => 3.5],
    ['name' => 'Iced Coffee', 'price' => 4],
    ['name' => 'Irish Coffee', 'price' => 6],
    ['name' => 'Water', 'price' => 3],
];

$food = [
    ['name' => 'Apple', 'price' => 2.5],
    ['name' => 'Watter melon', 'price' => 2.5],
    ['name' => 'Orange', 'price' => 3],
    ['name' => 'Black Berries', 'price' => 3.5],
    ['name' => 'Pear', 'price' => 4],
    ['name' => 'Lemon', 'price' => 6],
    ['name' => 'Peach', 'price' => 2],
];

if(!empty($_GET)){
    foreach ($_GET as $key=>$get){
        if(!in_array($key, ['order'])){ //to send only allowed keys in $_GET
            unset($_GET[$key]);
        }
    }

    if(!in_array($_GET['order'], ['food', 'drinks'])){  //to send only allowed values in 'order'
        unset($_GET['order']);
    }
}


// Use this function when you need to need an overview of these variables
function whatIsHappening() {
    echo '<h2>$_GET</h2>';

    var_dump($_GET);
    echo '<h2>$_POST</h2>';
    echo('<pre>');
    var_dump($_POST);
    echo ('</pre>');

    echo '<h2>$_COOKIE</h2>';
    var_dump($_COOKIE);
    echo '<h2>$_SESSION</h2>';
    var_dump($_SESSION);
}
//whatIsHappening();

class Order
{
    private Person $customer;

    /**
     * @var array
     */
    private array $products;
    private float $deliveryTime;
    private float $totalPrice = 0;
    public function __construct($person, $products, $deliveryTime)
    {
        $this->customer=$person;
        $this->products=$products;
        $this->deliveryTime=$deliveryTime;
    }
}

class Person
{
    private string $email;
    private stdClass $address;

    public function __construct(string $email, stdClass $address)
    {
        $this->email=$email;
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
}

class Request{
    private array $input;
    private string $email;
    private stdClass $address;
    private stdClass $products;
    private bool $fastDelivery=false;
    private array $errors=[];
    private array $validatedFormData = [];

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

        if (count($this->input['products']) == 0){
            array_push($this->errors, 'Please, chose at least one product.');
        }
        else{
            $this->products=$this->input['products'];
        }

        if ($this->input['deliveryTime'] != NULL){
            $this->fastDelivery=true;
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

    public function calculateTotalPrice()
    {
            $totalPrice = 0;
            foreach($this->products as $productNumber => $product) {
                $totalPrice +=$this->products[$productNumber]['price'];
            }
            if ($this->input['deliveryTime'] != NULL){
                $totalPrice +=2;
            }
            return $totalPrice;

    }

    public function getDeliveryTime()
    {
        if ($this->input['deliveryTime'] != NULL){
            $deliveryTime = date('m/d/Y h:i a', time()+ (2 * 24 * 60 * 60));

        } else {
            $deliveryTime = date('m/d/Y h:i a', time()+ (7 * 24 * 60 * 60));
        };
        return $deliveryTime;
    }

}

function totalValue ($productsList, &$deliveryTime){
    $chosenProduct = $_POST['products'];
    $totalPrice = 0;
    foreach($chosenProduct as $productNumber => $product) {
        $totalPrice +=$productsList[$productNumber]['price'];
    };
    if ($_POST['deliveryTime'] != NULL){
        $deliveryTime = date('m/d/Y h:i a', time()+ (2 * 24 * 60 * 60));
        $totalPrice += 2;
    };
    return $totalPrice;
};

function validate(): array
{
    $errors = [];
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $_POST['email'] = '';
        array_push($errors, 'Please, check your email address.');
    }
    if ($_POST['street'] == ''){
        $_POST['street'] = '';
        array_push($errors, 'Street field can not be empty.');
    }
    if ($_POST['city'] == ''){
        $_POST['city'] = '';
        array_push($errors, 'City field can not be empty.');
    }
    if (($_POST['streetnumber'] == '') || !(is_numeric(($_POST['streetnumber'])))){
        $_POST['streetnumber'] = '';
        array_push($errors, 'Street number field can not be empty and has to be a number.');
    }
    if (!(is_numeric(($_POST['zipcode'])))){
        $_POST['zipcode'] = '';
        array_push($errors, 'Zip code field can not be empty and has to be a number.');
     }

    if (count($_POST['products']) == 0){
        array_push($errors, 'Please, chose at least one product.');
    }

    $_SESSION["street"] = $_POST['street'];
    $_SESSION["city"] = $_POST['city'];
    $_SESSION["zipcode"] = $_POST['zipcode'];
    $_SESSION["streetnumber"] = $_POST['streetnumber'];

    return $errors;
}

function handleForm($productsList, &$deliveryTime): string
{
    $invalidFields = validate();
    if (!empty($invalidFields)) {
        return '<div class="alert alert-danger">' . implode(" </br> ", $invalidFields) .'</div>';
    }
    $chosenProduct = $_POST['products'];
    $orderList = [];
    $totalPrice = 0;
    foreach($chosenProduct as $productNumber => $product) {
        $totalPrice +=$productsList[$productNumber]['price'];
        array_push($orderList, $productsList[$productNumber]['name']);
    }

    if ($_POST['deliveryTime'] != NULL){
        $deliveryTime = date('d/m/Y h:i a', time()+ (2 * 24 * 60 * 60));;
        $totalPrice += 2;
    };

    if(!isset($_COOKIE['price']) && !isset($_COOKIE['orders'])){
        setcookie("price",  strval($totalPrice), time() + (10 * 365 * 24 * 60 * 60), "/"); //  set a cookie that expires in ten years
        setcookie("orders",  (implode(" ," , $orderList)), time() + (10 * 365 * 24 * 60 * 60), "/");
    } else {
        $newPrice = $_COOKIE['price'] . ',' . $totalPrice . ',';
        setcookie("price", $newPrice, time() + (10 * 365 * 24 * 60 * 60), "/");

        $newOrder = $_COOKIE['orders'] . ',' . (implode(",", $orderList));
        setcookie("orders", $newOrder);
    }
    $address1 = ['street'=>$_POST['street'], 'streetnumber'=>$_POST['streetnumber'], 'city'=>$_POST['city'], 'zipcode'=>$_POST['zipcode']];
    $person1 = new Person($_POST['email'], $address1);
  //  $order1 = new Order($person1, $_POST['products'], $_POST['deliveryTime'], $totalPrice);

    var_dump($person1);

    return ' <div class="alert alert-success"> 
            Your order is sumbited </br> Your address is: ' .$_POST['street'] . ' ' .$_POST['streetnumber'] . ' ' . ' ' .$_POST['city']
            .'</br>Your email is: ' .$_POST['email']
            .'</br> You have chosen: ' .implode(" , ", $orderList)
            .'</br> The total price is: &euro;' .number_format($totalPrice, 2)
            .'</br>Estimated delivery time: ' .$deliveryTime 
            .'</div>';
}

$confirmationMessage = "";

if (!empty($_POST)) {
    $request = new Request($_POST);

    if($request->validate()){
        $person = new Person($request->data('email'), $request->data('address'));
        $order = new Order($person, $request->data('products'), $request->data('fastDelivery'));



        $totalPrice = $request->calculateTotalPrice();
        $deliveryTime =$request->getDeliveryTime();
    }
    else{
        $errors = $request->errors();
    }

    //$confirmationMessage = handleForm(${$order}, $deliveryTime);
}
//setcookie("price",  '', time() + (10 * 365 * 24 * 60 * 60), "/"); //  clean a coockies
//setcookie("orders",  '', time() + (10 * 365 * 24 * 60 * 60), "/");

function getMostPopularItem(){
    if( !isset($_COOKIE['orders'])){
        return;
    };
    $orderedItems = (explode(',', $_COOKIE['orders']));
    $vals = array_count_values($orderedItems); //Counts all the (same) values of an array

    $mostPopularItem = '';
    $highestRate = 0;
    foreach ($vals as $key => $val){

        if ($val > $highestRate){
            $highestRate = $val;
            $mostPopularItem = $key;

        }
    }

    return '<h5 class="popular-order"> Most popular item, you have odered is: ' .$mostPopularItem . '.</h5>
            <h5 class="popular-order">You have ordered it '. $highestRate .' times. </h5>';


};

require 'form-view.php';
//test
$arr = [];

function addValue(&$array){
    $array[] = 'string';
}

addValue($arr);

//var_dump($arr);
