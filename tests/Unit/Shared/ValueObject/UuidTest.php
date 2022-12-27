<?php

namespace Tests\Unit\Costa\DomainPackage\ValueObject;

use Costa\DomainPackage\ValueObject\Uuid;
use InvalidArgumentException;
use Tests\Unit\TestCase;

class UuidTest extends TestCase
{
    public function testException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('<Costa\DomainPackage\ValueObject\Uuid> does not allow the value <test>.');
        $obj = new Uuid("test");
        $this->assertNotEmpty($obj);
    }

    public function testRandom(){
        $id = Uuid::random();
        $this->assertInstanceOf(Uuid::class, $id);
    }

    public function testToString(){
        $obj = new Uuid($id = 'b257aaca-75f2-4cdf-a96f-d438e1e891cc');
        $this->assertEquals($id, (string) $obj);
    }
}
