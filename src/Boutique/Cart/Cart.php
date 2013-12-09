<?php namespace Boutique\Cart;

use Boutique\Cart\Interfaces\CartInterface;
use Boutique\Cart\Interfaces\CartItemInterface;
use Boutique\Storage\Interfaces\StorageInterface;
use InvalidArgumentException;

class Cart implements CartInterface {

    protected static $id = 'cart';

    protected $storage;

    protected $cartItem;

    protected $items;

    public function __construct(StorageInterface $storage, CartItemInterface $item)
    {
        $this->storage  = $storage;
        $this->cartItem = $item;

        $this->restore();
    }

    public function add(array $data)
    {
        $item = $this->cartItem->make($data);

        $this->items[] = $item;

        $this->save();

        return $item;
    }

    public function update($key, $name, $value)
    {
        $item = $this->find($key);

        if( !$item )
            throw new InvalidArgumentException("Unable to find a cart item with the id of {$key}.");

        $item->$name = $value;

        $this->save();

        return $item->key;
    }

    public function get($key)
    {
        return $this->find($key);
    }

    public function trash($key)
    {
        $items =& $this->items;

       foreach ($items as $position => $item) {
            if ($key === $item->key) {
                unset($items[$position]);
            }
        }

        $this->save();
    }

    public function totalUniqueItems()
    {
        return count($this->items);
    }

    public function totalItems()
    {
        return array_sum(array_map(function($item) {
            return $item->quantity;
        }, $this->items));
    }

    public function total()
    {
        return (float) array_sum(array_map(function($item) {
                return $item->getTotalPrice();
        }, $this->items));
    }

    public function clear()
    {
        $this->items = array();
        $this->storage->flush(static::$id);
    }

    protected function find($key)
    {
        foreach ($this->items as $item) {
            if ($key === $item->key) {
                return $item;
            }
        }

        return null;
    }

    protected function save()
    {
        $this->storage->set(static::$id, $this->items);
    }

    protected function restore()
    {
        $items = $this->storage->get(static::$id);

        foreach( $items as $item )
        {
            $this->add($item);
        }
    }
}