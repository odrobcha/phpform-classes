<?php

// This file is your starting point (= since it's the index)
// It will contain most of the logic, to prevent making a messy mix in the html

// This line makes PHP behave in a more strict way
declare(strict_types=1);

//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);

session_start();
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

$orderMessage = "";
require('classes/Products.php');
$orderlist =  Products::drinks();
$chosenproducts='drinks';

if($_GET['orderlist'] == 'food'){
    $orderlist= Products::food();
    $chosenproducts='food';
}
if($_GET['orderlist'] == 'drinks'){
    $orderlist= Products::drinks();
    $chosenproducts='drinks';
}

require('classes/Order.php');
require('classes/Person.php');
require('classes/Request.php');


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

