<?php

namespace Tests\Unit\Core\Category\UseCase;

use Core\Category\Domain\Entity\Category;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Category\UseCase\{DeleteUseCase as UseCase};
use Shared\UseCase\DTO\Delete\{Input, Output};
use Shared\ValueObject\Uuid;
use Mockery;
use Shared\UseCase\Exception\{NotFoundException, UseCaseException};
use Tests\Unit\TestCase;

class DeleteUseCaseTest extends TestCase
{
    public function testExceptionNotFoundDeleteCategory()
    {
        $id = Uuid::random();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('ID ' . $id . ' not found.');

        /** @var CategoryRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn(null);
        $mockRepo->shouldReceive('delete')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id, 'test 2', 'test 3']);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testExceptionUseCaseDeleteCategory()
    {
        $this->expectException(UseCaseException::class);
        $this->expectExceptionMessage('The class Core\Category\UseCase\DeleteUseCase is wrong.');

        $id = Uuid::random();
        /** @var Category|Mockery\MockInterface */
        $mockEntity = Mockery::spy(Category::class, ['test', 'test', true, $id]);
        $mockEntity->shouldReceive('id')->andReturn($id);

        /** @var CategoryRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepo->shouldReceive('delete')->andReturn(false);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testDeleteCategory()
    {
        $id = Uuid::random();
        /** @var Category|Mockery\MockInterface */
        $mockEntity = Mockery::spy(Category::class, ['test', 'test', true, $id]);
        $mockEntity->shouldReceive('id')->andReturn($id);

        /** @var CategoryRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepo->shouldReceive('delete')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $retUseCase = $useCase->execute($mockInput);

        $this->assertInstanceOf(Output::class, $retUseCase);
        $this->assertTrue($retUseCase->success);
        $mockRepo->shouldHaveReceived('findById')->times(1);
        $mockRepo->shouldHaveReceived('delete')->times(1);
    }
}
