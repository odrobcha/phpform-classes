<?php


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