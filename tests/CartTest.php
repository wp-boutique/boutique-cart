<?php

use Boutique\Cart\Cart;
use Mockery as m;

class CartTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }
}
