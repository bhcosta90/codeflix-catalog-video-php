<?php

use Core\Test;
use Tests\Unit\TestCase;

class TestTest extends TestCase
{
    public function testCallMethodFoo(){
        $test = new Test();
        $this->assertEquals('123', $test->foo());
    }
}
