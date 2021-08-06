<?php

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