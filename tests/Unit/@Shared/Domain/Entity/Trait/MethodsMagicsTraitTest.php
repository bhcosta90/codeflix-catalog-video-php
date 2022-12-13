<?php

namespace Tests\Shared\Domain\Entity\Trait;

use Shared\Domain\Entity\Trait\MethodsMagicsTrait;
use Exception;
use Tests\Unit\TestCase;

class StubMethodsMagicsTrait
{
    use MethodsMagicsTrait;
}

class MethodsMagicsTraitTest extends TestCase
{
    public function testException(){
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Property test not found in class Tests\Shared\Domain\Entity\Trait\StubMethodsMagicsTrait');

        $obj = new StubMethodsMagicsTrait();
        $obj->test;
    }
}
