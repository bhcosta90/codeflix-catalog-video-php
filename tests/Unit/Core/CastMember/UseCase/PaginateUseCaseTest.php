<?php

namespace Tests\Unit\Core\CastMember\UseCase;

use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\CastMember\UseCase\{PaginateUseCase as UseCase, DTO\Paginate\Input};
use Costa\DomainPackage\UseCase\DTO\Paginate\Output;
use Mockery;
use stdClass;
use Tests\Unit\TestCase;

class PaginateUseCaseTest extends TestCase
{
    public function testPaginate()
    {
        /** @var CastMemberRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);

        $item = new stdClass();
        $item->id = '1b5433a3-7f7e-4b8a-b5e7-a55c78389129';
        $item->name = 'name';
        $item->description = 'description';
        $item->is_active = 'is_active';
        $item->created_at = 'created_at';

        $mockRepo->shouldReceive('paginate')->andReturn($this->getPaginationMockery([$item]));

        $mockInput = Mockery::mock(Input::class, [1]);

        $useCase = new UseCase(
            repository: $mockRepo
        );

        $retUseCase = $useCase->execute($mockInput);
        $this->assertInstanceOf(Output::class, $retUseCase);
        $this->assertCount(1, $retUseCase->items);
        $this->assertInstanceOf(stdClass::class, $retUseCase->items[0]);
        $mockRepo->shouldHaveReceived('paginate')->times(1);
    }
}
