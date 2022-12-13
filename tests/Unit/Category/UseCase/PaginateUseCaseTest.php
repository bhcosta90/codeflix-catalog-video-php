<?php

namespace Tests\Unit\Category\UseCase;

use Core\Category\Domain\Repository\CategoryRepository;
use Core\Category\UseCase\{PaginateUseCase as UseCase, DTO\Paginate\Input, DTO\Paginate\Output};
use Mockery;
use Tests\Unit\TestCase;

class PaginateUseCaseTest extends TestCase
{
    public function testPaginate()
    {
        /** @var CategoryRepository|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepository::class);
        $mockRepo->shouldReceive('paginate')->andReturn($this->getPaginationMockery());

        $mockInput = Mockery::mock(Input::class, [1]);

        $useCase = new UseCase(
            repository: $mockRepo
        );

        $retUseCase = $useCase->execute($mockInput);
        $this->assertInstanceOf(Output::class, $retUseCase);
        $this->assertCount(0, $retUseCase->items);
        $mockRepo->shouldHaveReceived('paginate')->times(1);
    }
}
