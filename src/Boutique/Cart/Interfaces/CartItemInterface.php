<?php namespace Boutique\Cart\Interfaces;

interface CartItemInterface {

    public function key(array $data);

    public function make(array $data);

    public function getTotalPrice();
}