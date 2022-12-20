<?php

namespace Tests\Unit;

use Exception;
use Shared\Domain\Repository\PaginationInterface;
use Mockery;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Shared\UseCase\Interfaces\DatabaseTransactionInterface;
use stdClass;

abstract class TestCase extends PHPUnitTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @return DatabaseTransactionInterface|Mockery\MockInterface */
    protected function getDatabaseTransactionInterface($success = true)
    {
        /** @var DatabaseTransactionInterface|Mockery\MockInterface */
        $mock = Mockery::spy(stdClass::class, DatabaseTransactionInterface::class);
        if ($success) {
            $mock->shouldReceive('commit');
        } else {
            $mock->shouldReceive('commit')->andThrowExceptions([new Exception('database error')]);
        }
        $mock->shouldReceive('rollback');
        return $mock;
    }

    /** @return PaginationInterface|Mockery\MockInterface */
    protected function getPaginationMockery(
        array $items = [],
    ) {
        /** @var PaginationInterface|Mockery\MockInterface */
        $mockPaginate = Mockery::spy(stdClass::class, PaginationInterface::class);
        $mockPaginate->shouldReceive('items')->andReturn($items);
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
