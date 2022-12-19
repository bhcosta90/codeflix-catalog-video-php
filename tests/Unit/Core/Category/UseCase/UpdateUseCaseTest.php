<?php

namespace Tests\Unit\Core\Category\UseCase;

use Core\Category\Domain\Entity\CategoryEntity;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Category\UseCase\{UpdateUseCase as UseCase, DTO\Update\Input, DTO\Update\Output};
use DateTime;
use Shared\ValueObject\Uuid;
use Mockery;
use Shared\UseCase\Exception\{NotFoundException, UseCaseException};
use Tests\Unit\TestCase;

class UpdateUseCaseTest extends TestCase
{
    public function testExceptionNotFoundUpdateCategory()
    {
        $id = Uuid::random();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('ID ' . $id . ' not found.');

        /** @var CategoryRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn(null);
        $mockRepo->shouldReceive('update')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id, 'test 2', 'test 3', true]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testExceptionUseCaseUpdateCategory()
    {
        $this->expectException(UseCaseException::class);
        $this->expectExceptionMessage('The class Core\Category\UseCase\UpdateUseCase is wrong.');

        $id = Uuid::random();
        /** @var CategoryEntity|Mockery\MockInterface */
        $mockEntity = Mockery::spy(CategoryEntity::class, ['test', 'test', true, $id]);
        $mockEntity->shouldReceive('id')->andReturn($id);

        /** @var CategoryRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepo->shouldReceive('update')->andReturn(false);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id, 'test 2', 'test 3', true]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testUpdateCategory()
    {
        $id = Uuid::random();
        /** @var CategoryEntity|Mockery\MockInterface */
        $mockEntity = Mockery::spy(CategoryEntity::class, ['test', 'test', true, $id]);
        $mockEntity->shouldReceive('id')->andReturn($id)
            ->shouldReceive('createdAt')->andReturn((new DateTime())->format('Y-m-d H:i:s'));

        /** @var CategoryRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepo->shouldReceive('update')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id, 'test 2', 'test 3', true]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $retUseCase = $useCase->execute($mockInput);

        $this->assertInstanceOf(Output::class, $retUseCase);
        $this->assertNotEmpty($retUseCase->created_at);
        $this->assertEquals($id, $retUseCase->id);
        $this->assertTrue($retUseCase->is_active);
        $mockRepo->shouldHaveReceived('findById')->times(1);
        $mockRepo->shouldHaveReceived('update')->times(1);
    }
}
