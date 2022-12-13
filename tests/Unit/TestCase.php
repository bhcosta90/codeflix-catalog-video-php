<?php

namespace Tests\Unit;

use Shared\Domain\Repository\PaginationInterface;
use Mockery;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use stdClass;

abstract class TestCase extends PHPUnitTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @return PaginationInterface|Mockery\MockInterface */
    protected function getPaginationMockery(){
        /** @var PaginationInterface|Mockery\MockInterface */
        $mockPaginate = Mockery::spy(stdClass::class, PaginationInterface::class);
        $mockPaginate->shouldReceive('items')->andReturn([]);
        $mockPaginate->shouldReceive('total')->andReturn(0);
        $mockPaginate->shouldReceive('firstPage')->andReturn(1);
        $mockPaginate->shouldReceive('perPage')->andReturn(15);
        $mockPaginate->shouldReceive('currentPage')->andReturn(1);
        $mockPaginate->shouldReceive('lastPage')->andReturn(1);
        $mockPaginate->shouldReceive('to')->andReturn(1);
        $mockPaginate->shouldReceive('from')->andReturn(1);

        return $mockPaginate;
    }
}
