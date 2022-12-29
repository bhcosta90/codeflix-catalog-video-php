<?php

namespace Tests\Unit\Core\Video\UseCase;

use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Factory\CastMemberFactoryInterface;
use Core\Video\Factory\CategoryFactoryInterface;
use Core\Video\Factory\GenreFactoryInterface;
use Core\Video\UseCase\CreateUseCase;
use Core\Video\UseCase\DTO\Create as DTO;
use Core\Video\UseCase\Exceptions\CastMemberNotFound;
use Core\Video\UseCase\Exceptions\CategoryGenreNotFound;
use Core\Video\UseCase\Exceptions\CategoryNotFound;
use Core\Video\UseCase\Exceptions\GenreNotFound;
use Costa\DomainPackage\UseCase\Exception\UseCaseException;
use Costa\DomainPackage\UseCase\Interfaces\FileStorageInterface;
use Mockery;
use stdClass;
use Tests\Unit\Core\Video\Event\VideoEventManagerInterface;
use Tests\Unit\TestCase;

class CreateUseCaseTest extends TestCase
{
    public function test_constructor()
    {
        new CreateUseCase(
            repository: $this->createMockRepository(),
            transaction: $this->getDatabaseTransactionInterface(),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );
        $this->assertTrue(true);
    }

    public function testExecInputExceptionUseCase()
    {
        $this->expectException(UseCaseException::class);
        $this->expectExceptionMessage('The class Core\Video\UseCase\CreateUseCase is wrong.');

        $useCase = new CreateUseCase(
            repository: $this->createMockRepository(false),
            transaction: $this->getDatabaseTransactionInterface(),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );
        $useCase->execute($this->createMockInput());
    }

    public function testExecInput()
    {
        $useCase = new CreateUseCase(
            repository: $this->createMockRepository(),
            transaction: $this->getDatabaseTransactionInterface(timesCallCommit: 1),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(['123', '456'], ['123', '456']),
            genreFactory: $this->createMockGenreFactory(['123', '456']),
            castMemberFactory: $this->createMockCastMemberFactory(['654', '321']),
        );
        $useCase->execute($this->createMockInput(
            categories: ['123'],
            genres: ['123', '456'],
            castMembers: ['654', '321'],
        ));
        $this->assertTrue(true);
    }

    public function testExecOutput()
    {
        $useCase = new CreateUseCase(
            repository: $mockRepo = $this->createMockRepository(),
            transaction: $this->getDatabaseTransactionInterface(timesCallCommit: 1),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );
        $response = $useCase->execute($this->createMockInput());
        $this->assertInstanceOf(DTO\Output::class, $response);

        $mockRepo->shouldHaveReceived('insert')->times(1);
        $mockRepo->shouldHaveReceived('updateMedia')->times(1);
    }

    public function testExecOutputWithImages()
    {
        $useCase = new CreateUseCase(
            repository: $mockRepo = $this->createMockRepository(),
            transaction: $this->getDatabaseTransactionInterface(timesCallCommit: 1),
            storage: $mockStorage = $this->createMockFileStorage(true),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );
        $response = $useCase->execute($this->createMockInput(
            thumbFile: ['tmp' => '/tmp/test.txt'],
            thumbHalf: ['tpm' => '/tmp/test.txt'],
            bannerFile: ['tpm' => '/tmp/test.txt'],
            trailerFile: ['tpm' => '/tmp/test.txt'],
            videoFile: ['tpm' => '/tmp/test.txt'],
        ));
        $this->assertInstanceOf(DTO\Output::class, $response);

        $mockRepo->shouldHaveReceived('insert')->times(1);
        $mockRepo->shouldHaveReceived('updateMedia')->times(1);
        $mockStorage->shouldHaveReceived('store')->times(5);
    }

    public function testExecExceptionCategoryWithoutGenre()
    {
        try {
            $useCase = new CreateUseCase(
                repository: $this->createMockRepository(),
                transaction: $this->getDatabaseTransactionInterface(timesCallRollback: 1),
                storage: $this->createMockFileStorage(),
                eventManager: $this->createMockEventManager(),
                categoryFactory: $this->createMockCategoryFactory(['789', '987', '444']),
                genreFactory: $this->createMockGenreFactory(['123', '456'], ['789', '987']),
                castMemberFactory: $this->createMockCastMemberFactory(),
            );
            $useCase->execute($this->createMockInput(
                categories: ['789', '987', '444'],
                genres: ['123', '456']
            ));
        } catch (CategoryGenreNotFound $e) {
            $this->assertEquals(['789', '987', '444'], array_values($e->categories));
        }
    }

    public function testExecExceptionCategories()
    {
        $this->expectException(CategoryNotFound::class);
        $this->expectExceptionMessage('Categories not found');
        $useCase = new CreateUseCase(
            repository: $this->createMockRepository(),
            transaction: $this->getDatabaseTransactionInterface(timesCallRollback: 1),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );
        $useCase->execute($this->createMockInput(
            categories: ['123', '456']
        ));
    }

    public function testExecExceptionGenres()
    {
        $this->expectException(GenreNotFound::class);
        $this->expectExceptionMessage('Genres not found');
        $useCase = new CreateUseCase(
            repository: $this->createMockRepository(),
            transaction: $this->getDatabaseTransactionInterface(timesCallRollback: 1),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );
        $useCase->execute($this->createMockInput(
            genres: ['123', '456']
        ));
    }

    public function testExecExceptionCastMembers()
    {
        $this->expectException(CastMemberNotFound::class);
        $this->expectExceptionMessage('Cast Members not found');
        $useCase = new CreateUseCase(
            repository: $this->createMockRepository(),
            transaction: $this->getDatabaseTransactionInterface(timesCallRollback: 1),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );
        $useCase->execute($this->createMockInput(
            castMembers: ['123', '456']
        ));
    }

    protected function createMockRepository($success = true)
    {
        $mock = Mockery::spy(stdClass::class, VideoRepositoryInterface::class);
        $mock->shouldReceive('insert')->andReturn($success);

        return $mock;
    }

    protected function createMockFileStorage()
    {
        return Mockery::spy(stdClass::class, FileStorageInterface::class);
    }

    protected function createMockEventManager()
    {
        return Mockery::spy(stdClass::class, VideoEventManagerInterface::class);
    }

    protected function createMockCategoryFactory(array $data = [], array $genres = [])
    {
        $mock = Mockery::spy(stdClass::class, CategoryFactoryInterface::class);
        $mock->shouldReceive('findByIds')->andReturn($data);
        $mock->shouldReceive('findByIdsWithGenres')->andReturn($genres);

        return $mock;
    }

    protected function createMockGenreFactory(array $data = [])
    {
        $mock = Mockery::spy(stdClass::class, GenreFactoryInterface::class);
        $mock->shouldReceive('findByIds')->andReturn($data);

        return $mock;
    }

    protected function createMockCastMemberFactory(array $data = [])
    {
        $mock = Mockery::spy(stdClass::class, CastMemberFactoryInterface::class);
        $mock->shouldReceive('findByIds')->andReturn($data);

        return $mock;
    }

    protected function createMockInput(
        array $categories = [],
        array $genres = [],
        array $castMembers = [],
        array $thumbFile = [],
        array $thumbHalf = [],
        array $bannerFile = [],
        array $trailerFile = [],
        array $videoFile = [],
    ) {
        return Mockery::spy(DTO\Input::class, [
            'test',
            'test',
            2020,
            50,
            true,
            'L',
            $categories,
            $genres,
            $castMembers,
            $thumbFile,
            $thumbHalf,
            $bannerFile,
            $trailerFile,
            $videoFile,
        ]);
    }
}
