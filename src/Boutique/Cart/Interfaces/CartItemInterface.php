<?php namespace Boutique\Cart\Interfaces;

interface CartItemInterface {

    public function make(array $data);

    public function getTotalPrice();
}