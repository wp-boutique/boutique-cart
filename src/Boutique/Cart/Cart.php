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
        $item = new $this->cartItem($data);

        $this->items[] = $item;

        $this->save();

        return $item;
    }

    public function update($key, $key, $value)
    {
        $item = $this->find($id);

        if( !$item )
            throw new InvalidArgumentException("Unable to find a cart item with the id of {$id}.");

        $item->$key = $value;

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
            if ($itemId === $item->id) {
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

    protected function save()
    {
        $this->storage->set(static::$cart, $this->items);
    }

    protected function restore()
    {
        $items = $this->storage->get(static::$cart);

        foreach( $items as $item )
        {
            $this->add($item);
        }
    }
}