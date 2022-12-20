<?php

namespace Tests\Unit\Core\Genre\UseCase;

use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Genre\Factory\CategoryFactoryInterface;
use Core\Genre\UseCase\{CreateUseCase as UseCase, DTO\Create\Input, DTO\Create\Output};
use Core\Genre\UseCase\Exceptions\CategoryNotFound;
use Exception;
use Shared\UseCase\Exception\UseCaseException;
use Mockery;
use stdClass;
use Tests\Unit\TestCase;

class CreateUseCaseTest extends TestCase
{
    public function testCreateNewGenreException()
    {
        try {
            /** @var GenreRepositoryInterface|Mockery\MockInterface */
            $mockRepo = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
            $mockRepo->shouldReceive('insert')->andReturn(false);

            /** @var Input|Mockery\MockInterface */
            $mockInput = Mockery::mock(Input::class, ['test', []]);

            $useCase = new UseCase(
                repository: $mockRepo,
                transaction: $this->getDatabaseTransactionInterface(),
                categoryFactory: $this->mockCategoryFactoryInterface(),
            );

            $useCase->execute($mockInput);
        } catch (UseCaseException $e) {
            $this->assertEquals($e->getMessage(), 'The class Core\Genre\UseCase\CreateUseCase is wrong.');
        }

        try {
            /** @var GenreRepositoryInterface|Mockery\MockInterface */
            $mockRepo = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
            $mockRepo->shouldReceive('insert')->andReturn(true);

            /** @var Input|Mockery\MockInterface */
            $mockInput = Mockery::mock(Input::class, ['test', []]);

            $useCase = new UseCase(
                repository: $mockRepo,
                transaction: $this->getDatabaseTransactionInterface(),
                categoryFactory: $this->mockCategoryFactoryInterface(['123', '456']),
            );

            $useCase->execute($mockInput);
        } catch (CategoryNotFound $e) {
            $this->assertEquals($e->getMessage(), 'Categories not found');
            $this->assertEquals(['123', '456'], $e->categories);
        }
    }

    public function testDatabaseException(){
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('database error');

        /** @var GenreRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('insert')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['test', ['123', '456']]);

        $useCase = new UseCase(
            repository: $mockRepo,
            transaction: $this->getDatabaseTransactionInterface(false),
            categoryFactory: $this->mockCategoryFactoryInterface(),
        );

        $useCase->execute($mockInput);
    }

    public function testCreateNewGenre()
    {
        /** @var GenreRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('insert')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['test', ['123', '456']]);

        $useCase = new UseCase(
            repository: $mockRepo,
            transaction: $this->getDatabaseTransactionInterface(),
            categoryFactory: $this->mockCategoryFactoryInterface(),
        );

        $retUseCase = $useCase->execute($mockInput);

        $this->assertInstanceOf(Output::class, $retUseCase);
        $this->assertNotEmpty($retUseCase->id);
        $this->assertNotEmpty($retUseCase->created_at);
        $this->assertEquals('test', $retUseCase->name);
        $this->assertEquals(['123', '456'], $retUseCase->categories);
        $this->assertTrue($retUseCase->is_active);
        $mockRepo->shouldHaveReceived('insert')->times(1);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['test', ['123', '456'], false]);
        $retUseCase = $useCase->execute($mockInput);
        $this->assertFalse($retUseCase->is_active);
    }

    /** @return CategoryFactoryInterface|Mockery\MockInterface */
    private function mockCategoryFactoryInterface(array $categories = [])
    {
        $mock = Mockery::spy(stdClass::class, CategoryFactoryInterface::class);
        $mock->shouldReceive('findByIds')->andReturn($categories);
        return $mock;
    }
}
