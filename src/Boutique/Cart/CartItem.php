<?php namespace Boutique\Cart;

use InvalidArgumentException;

class CartItem {

    /**
     * Keys to ignore when generating key
     * @var array
     */
    protected static $ignoreKeys = ['quantity'];

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
     * Set item key
     */
    protected function setKey()
    {
        $hashData = $this->attributes;

        foreach (static::$ignoreKeys as $key)
        {
            unset($hashData[$key]);
        }

        $hash = sha1(serialize($hashData));

        $this->key = $hash;

        return $hash;
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
