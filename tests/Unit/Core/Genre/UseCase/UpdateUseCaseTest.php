<?php

namespace Tests\Unit\Core\Genre\UseCase;

use Core\Genre\Domain\Entity\Genre;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Genre\Factory\CategoryFactoryInterface;
use Core\Genre\Factory\GenreFactoryInterface;
use Core\Genre\UseCase\{UpdateUseCase as UseCase, DTO\Update\Input, DTO\Update\Output};
use Core\Genre\UseCase\Exceptions\CategoryNotFound;
use Core\Genre\UseCase\Exceptions\GenreNotFound;
use DateTime;
use Exception;
use Shared\UseCase\Exception\UseCaseException;
use Mockery;
use Shared\UseCase\Exception\NotFoundException;
use Shared\ValueObject\Uuid;
use stdClass;
use Tests\Unit\TestCase;

class UpdateUseCaseTest extends TestCase
{
    public function testExceptionNotFoundUpdateGenre()
    {
        $id = Uuid::random();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('ID ' . $id . ' not found.');

        /** @var GenreRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn(null);
        $mockRepo->shouldReceive('update')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id, 'test 2', [], true]);

        $useCase = new UseCase(
            repository: $mockRepo,
            transaction: $this->getDatabaseTransactionInterface(),
            categoryFactory: $this->mockCategoryFactoryInterface(),
        );

        $useCase->execute($mockInput);
    }

    public function testUpdateException()
    {
        $id = Uuid::random();

        $mockEntity = Mockery::spy(Genre::class, ['test', true, $id, new DateTime(), []]);
        $mockEntity->shouldReceive('id')->andReturn($id)
            ->shouldReceive('createdAt')->andReturn((new DateTime())->format('Y-m-d H:i:s'));

        try {
            /** @var GenreRepositoryInterface|Mockery\MockInterface */
            $mockRepo = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
            $mockRepo->shouldReceive('findById')->andReturn($mockEntity);

            /** @var Input|Mockery\MockInterface */
            $mockInput = Mockery::mock(Input::class, [$id, 'test', []]);

            $useCase = new UseCase(
                repository: $mockRepo,
                transaction: $this->getDatabaseTransactionInterface(),
                categoryFactory: $this->mockCategoryFactoryInterface([]),
            );

            $useCase->execute($mockInput);
        } catch (UseCaseException $e) {
            $this->assertEquals($e->getMessage(), 'The class Core\Genre\UseCase\UpdateUseCase is wrong.');
        }

        try {
            /** @var GenreRepositoryInterface|Mockery\MockInterface */
            $mockRepo = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
            $mockRepo->shouldReceive('findById')->andReturn($mockEntity);
            $mockRepo->shouldReceive('update')->andReturn(true);

            /** @var Input|Mockery\MockInterface */
            $mockInput = Mockery::mock(Input::class, [$id, 'test', []]);

            $useCase = new UseCase(
                repository: $mockRepo,
                transaction: $this->getDatabaseTransactionInterface(timesCallCommit: 1),
                categoryFactory: $this->mockCategoryFactoryInterface(['123', '456']),
            );

            $useCase->execute($mockInput);
        } catch (CategoryNotFound $e) {
            $this->assertEquals($e->getMessage(), 'Categories not found');
            $this->assertEquals(['123', '456'], $e->categories);
        }
    }

    public function testUpdateCategory()
    {
        $id = Uuid::random();
        $mockEntity = Mockery::spy(Genre::class, ['test', true, $id, new DateTime(), []]);
        $mockEntity->shouldReceive('id')->andReturn($id)
            ->shouldReceive('createdAt')->andReturn((new DateTime())->format('Y-m-d H:i:s'));

        /** @var GenreRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepo->shouldReceive('update')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id, 'test 2', ['789', '987'], true]);

        $useCase = new UseCase(
            repository: $mockRepo,
            transaction: $this->getDatabaseTransactionInterface(timesCallCommit: 1),
            categoryFactory: $this->mockCategoryFactoryInterface(['789', '987']),
        );

        $retUseCase = $useCase->execute($mockInput);

        $this->assertInstanceOf(Output::class, $retUseCase);
        $this->assertNotEmpty($retUseCase->created_at);
        $this->assertEquals($id, $retUseCase->id);
        $this->assertEquals([], array_values($retUseCase->categories));
        $this->assertTrue($retUseCase->is_active);
        $mockRepo->shouldHaveReceived('findById')->times(1);
        $mockRepo->shouldHaveReceived('update')->times(1);
    }

    /** @return CategoryFactoryInterface|Mockery\MockInterface */
    private function mockCategoryFactoryInterface(array $categories = [])
    {
        $mock = Mockery::spy(stdClass::class, CategoryFactoryInterface::class);
        $mock->shouldReceive('findByIds')->andReturn($categories);
        return $mock;
    }
}
