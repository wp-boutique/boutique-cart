<?php namespace Boutique\Cart\Interfaces;

interface CartInterface {

    public function add(array $data);

    public function update($key, $name, $value);

    public function get($key);

    public function trash($key);

    public function totalUniqueItems();

    public function totalItems();

    public function total();

    public function clear();

}