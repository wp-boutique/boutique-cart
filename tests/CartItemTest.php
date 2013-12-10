<?php

use Boutique\Cart\CartItem;

class CartItemTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->data = array(
            'name'     => 'Foo',
            'quantity' => 1,
            'price'    => 100.00,
            'options'  => ['size' => 'L', 'color' => 'red']
        );
    }

    public function testMakeMethod()
    {
        $item = new CartItem;

        $new = $item->make($this->data);

        $this->assertEquals($item->key, null);

        $this->assertTrue( !!$new->key );
    }

    public function testGetItemKey()
    {
        $item = new CartItem($this->data);

        $this->assertTrue( !!$item->key );
    }

    public function testSetAttributes()
    {
        $item = new CartItem;

        $item->name = 'foo';
        $this->assertSame($item->name, 'foo');
    }

    public function testUpdateKey()
    {
        $item = new CartItem($this->data);

        // Test a key exists
        $key = $item->key;
        $this->assertTrue( !!$key );

        // Rename product
        $item->name = 'Bar';

        $this->assertTrue( !!$key );
        // Test that key has changed
        $this->assertNotSame($item->key, $key);

        $key = $item->key;
        // Change item qty
        $item->quantity = 10;
        // Test that key are unchanged
        $this->assertSame($item->key, $key);
    }

    public function testGetTotalPrice()
    {
        $item = new CartItem( $this->data );

        $this->assertEquals($item->getTotalPrice(), 100.00);

        $item->quantity = 10;

        $this->assertEquals($item->getTotalPrice(), 1000.00);
    }

}
