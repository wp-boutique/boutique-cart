<?php

use Boutique\Cart\Cart;
use Mockery as m;

class CartTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        $this->storage  = m::mock('Boutique\Storage\Interfaces\StorageInterface');
        $this->storage->shouldReceive('get')->once()->andReturn(array());
        $this->storage->shouldReceive('set');

        $this->cartItem = m::mock('Boutique\Cart\Interfaces\CartItemInterface');
        $this->cartItem->shouldReceive('make')->andReturn($this->cartItem);

        $this->cart = new Cart($this->storage, $this->cartItem);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testAddItemToCart()
    {
        $return = $this->addItemToCart();
        $this->assertTrue($return instanceof Boutique\Cart\Interfaces\CartItemInterface);
    }

    public function testUniqueItemsIsInCart()
    {
        $this->addItemToCart();
        $this->assertEquals(1, $this->cart->totalUniqueItems());
    }

    public function testTotalItemsInCart()
    {
        $this->cartItem->quantity = 2;

        $this->addItemToCart();
        $this->addItemToCart();

        $this->assertEquals(4, $this->cart->totalItems());
        $this->assertEquals(2, $this->cart->totalUniqueItems());
    }

    public function testUpdateCartItem()
    {
        $this->cartItem->quantity = 1;
        $this->cartItem->key = 'foo';

        $this->addItemToCart();

        $return = $this->cart->update('foo', 'quantity', 2);

        $this->assertEquals('foo', $return);
        $this->assertEquals(2, $this->cart->get('foo')->quantity);
    }

    public function testGetCartItem()
    {
        $this->cartItem->key = 'foo';

        $this->addItemToCart();

        $this->assertTrue($this->cart->get('foo') instanceof  Boutique\Cart\Interfaces\CartItemInterface);
    }

    public function testTrashItem()
    {
        $this->testGetCartItem();

        $this->cart->trash('foo');

        $this->assertEquals($this->cart->totalUniqueItems(), 0);
    }

    public function testCartTotal()
    {
        $this->cartItem->shouldReceive('getTotalPrice')->andReturn(200.00);
        $this->addItemToCart();

        $this->assertEquals($this->cart->total(), 200.00);
        $this->assertTrue(is_float($this->cart->total()));
    }

    public function testGetAllItems()
    {
        $this->addItemToCart();

        $all = $this->cart->all();

        $this->assertTrue( is_array($all) );
        $this->assertEquals(count($all), 1);
    }

    protected function addItemToCart()
    {
        return $this->cart->add(array('foo'));
    }
}
