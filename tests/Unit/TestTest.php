<?php

use Core\Test;
use PHPUnit\Framework\TestCase;

class TestTest extends TestCase
{
    public function testCallMethodFoo(){
        $teste = new Test();
        $this->assertEquals('123', $teste->foo());
    }
}
