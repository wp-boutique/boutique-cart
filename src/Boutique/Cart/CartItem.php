<?php namespace Boutique\Cart;

use InvalidArgumentException;
use Boutique\Cart\Interfaces\CartItemInterface;

class CartItem implements CartItemInterface {

    /**
     * Keys to ignore when generating key
     * @var array
     */
    protected static $ignoreKeys = ['quantity', 'key'];

    /**
     * Item attributes
     * @var array
     */
    protected $attributes = array();

    /**
     * Cart item construtor
     * @param array $data
     */
    public function __construct(array $data=null)
    {
        if( $data )
        {
            if( !array_key_exists('quantity', $data) OR !array_key_exists('name', $data) OR !array_key_exists('price', $data) )
                throw new InvalidArgumentException("These fields is required: 'quantity', 'price' and 'name'.");

            foreach( $data as $key => $value )
            {
                $this->$key = $value;
            }

            $this->setKey();
        }

        return $this;
    }

    /**
     * Make new cart item
     * @param  array  $data
     * @return CartItem
     */
    public function make(array $data)
    {
        return new self($data);
    }

    /**
     * Get item total price
     * @return float
     */
    public function getTotalPrice()
    {
        return (float) $this->price * $this->quantity;
    }

    /**
     * Generate key
     * @param  array  $data
     * @return string
     */
    public function key(array $data)
    {
        foreach (static::$ignoreKeys as $key)
        {
            unset($data[$key]);
        }

        $hash = sha1(serialize($data));

        return $hash;
    }

    /**
     * Set item key
     */
    protected function setKey()
    {
        $key = $this->key($this->attributes);
        $this->attributes['key'] = $key;

        return $key;
    }

    /**
     * Set item attributes
     * @param  string $name
     * @param  string $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        if( $name === 'key' )
            return null;

        $this->attributes[$name] = $value;
        $this->setKey();
    }

    /**
     * Get item attribute
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        if( !isset($this->attributes[$name]) )
            return null;

        return $this->attributes[$name];
    }
}
