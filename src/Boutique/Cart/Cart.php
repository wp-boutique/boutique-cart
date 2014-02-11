<?php namespace Boutique\Cart;

use Boutique\Cart\Interfaces\CartInterface;
use Boutique\Cart\Interfaces\CartItemInterface;
use Boutique\Storage\Interfaces\StorageInterface;
use InvalidArgumentException;

class Cart implements CartInterface {

    /**
     * Cart identifier for the
     * Storage implementation
     * @var string
     */
    protected static $id = 'cart';

    /**
     * Storage implemantation
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Cart Item implementation
     * @var CartItemInterface
     */
    protected $cartItem;

    /**
     * Cart items
     * @var array
     */
    protected $items = [];

    /**
     * Cart constructor
     * @param StorageInterface  $storage
     * @param CartItemInterface $item
     */
    public function __construct(StorageInterface $storage, CartItemInterface $item)
    {
        $this->storage  = $storage;
        $this->cartItem = $item;

        $this->restore();
    }

    /**
     * Add item to cart
     * @param array $data
     */
    public function add(array $data)
    {
        $item = $this->cartItem->make($data);

        if( $existing = $this->find($item->key) )
        {
            return $this->addQuantity($existing, $data['quantity']);
        }

        $this->items[] = $item;

        $this->save();

        return $item;
    }

    /**
     * Add to quantity on an existing item
     * @param CartItemInterface $item
     * @param integer           $quantity
     */
    protected function addQuantity(CartItemInterface $item, $quantity)
    {
        $this->update($item->key, 'quantity', $item->quantity + $quantity);

        return $this->find($item->key);
    }

    /**
     * Update cart item attribute
     * @param  string $key
     * @param  string $name
     * @param  string $value
     * @return mixed
     */
    public function update($key, $name, $value)
    {
        $item = $this->find($key);

        if( !$item )
            throw new InvalidArgumentException("Unable to find a cart item with the id of {$key}.");

        $item->$name = $value;

        $this->save();

        return $item->key;
    }

    /**
     * Get cart item by key
     * @param  string $key
     * @return CartItemInterface
     */
    public function get($key)
    {
        return $this->find($key);
    }

    /**
     * Get all cart items
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Trash a cart item by key
     * @param  string $key
     * @return void
     */
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

    /**
     * Get total unique items in cart
     * @return integer
     */
    public function totalUniqueItems()
    {
        return count($this->items);
    }

    /**
     * Total items in cart
     * @return integer
     */
    public function totalItems()
    {
        return array_sum(array_map(function($item) {
            return $item->quantity;
        }, $this->items));
    }

    /**
     * Cart total value
     * @return float
     */
    public function total()
    {
        return (float) array_sum(array_map(function($item) {
                return $item->getTotalPrice();
        }, $this->items));
    }

    /**
     * Clear cart
     * @return void
     */
    public function clear()
    {
        $this->items = array();
        $this->storage->trash(static::$id);
    }

    /**
     * Find cart item by key
     * @param  string $key
     * @return mixed
     */
    protected function find($key)
    {
        foreach ($this->items as $item) {
            if ($key === $item->key) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Save cart to storage implemantation
     * @return void
     */
    protected function save()
    {
        $this->storage->set(static::$id, $this->items);
    }

    /**
     * Restore cart from storage implementation
     * @return void
     */
    protected function restore()
    {
        $items = $this->storage->get(static::$id, array());

        foreach( $items as $item )
        {
            $this->add($item);
        }
    }
}
