<?php

namespace Tests\Unit\Core\Video\UseCase;

use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Factory\CastMemberFactoryInterface;
use Core\Video\Factory\CategoryFactoryInterface;
use Core\Video\Factory\GenreFactoryInterface;
use Core\Video\UseCase\DTO\Update as DTO;
use Core\Video\UseCase\Exceptions\CastMemberNotFound;
use Core\Video\UseCase\Exceptions\CategoryGenreNotFound;
use Core\Video\UseCase\Exceptions\CategoryNotFound;
use Core\Video\UseCase\Exceptions\GenreNotFound;
use Core\Video\UseCase\UpdateUseCase as UseCase;
use Costa\DomainPackage\UseCase\Exception\NotFoundException;
use Costa\DomainPackage\UseCase\Exception\UseCaseException;
use Costa\DomainPackage\UseCase\Interfaces\FileStorageInterface;
use Costa\DomainPackage\ValueObject\Uuid;
use Mockery;
use Tests\Unit\Core\Video\Event\VideoEventManagerInterface;
use Tests\Unit\TestCase;

class UpdateUseCaseTest extends TestCase
{
    public function testExceptionNotFoundUpdateVideo()
    {
        $id = Uuid::random();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('ID '.$id.' not found.');

        /** @var VideoRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, VideoRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn(null);
        $mockRepo->shouldReceive('update')->andReturn(true);

        $useCase = new UseCase(
            repository: $this->createMockRepository(),
            transaction: $this->getDatabaseTransactionInterface(),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );

        $useCase->execute($this->createMockInput($id));
    }

    public function testExceptionUseCaseUpdateVideo()
    {
        $this->expectException(UseCaseException::class);
        $this->expectExceptionMessage('The class Core\Video\UseCase\UpdateUseCase is wrong.');

        $id = Uuid::random();
        $entity = $this->getEntity($id);

        /** @var VideoRepositoryInterface|Mockery\MockInterface */
        $mockRepo = $this->createMockRepository(false);
        $mockRepo->shouldReceive('findById')->andReturn($entity);
        $useCase = new UseCase(
            repository: $mockRepo,
            transaction: $this->getDatabaseTransactionInterface(timesCallRollback: 1),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );

        $useCase->execute($this->createMockInput($id));
    }

    public function testUpdateVideo()
    {
        $id = Uuid::random();
        $entity = $this->getEntity($id);

        /** @var VideoRepositoryInterface|Mockery\MockInterface */
        $mockRepo = $this->createMockRepository();
        $mockRepo->shouldReceive('findById')->andReturn($entity);

        $useCase = new UseCase(
            repository: $mockRepo,
            transaction: $this->getDatabaseTransactionInterface(timesCallCommit: 1),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );

        $retUseCase = $useCase->execute($this->createMockInput($id));

        $this->assertInstanceOf(DTO\Output::class, $retUseCase);
        $this->assertNotEmpty($retUseCase->created_at);
        $this->assertEquals($id, $retUseCase->id);
        $mockRepo->shouldHaveReceived('findById')->times(1);
        $mockRepo->shouldHaveReceived('update')->times(1);
    }

    public function testExecExceptionCategoryWithoutGenre()
    {
        $id = Uuid::random();
        $entity = $this->getEntity($id);

        /** @var VideoRepositoryInterface|Mockery\MockInterface */
        $mockRepo = $this->createMockRepository();
        $mockRepo->shouldReceive('findById')->andReturn($entity);

        try {
            $useCase = new UseCase(
                repository: $mockRepo,
                transaction: $this->getDatabaseTransactionInterface(timesCallRollback: 1),
                storage: $this->createMockFileStorage(),
                eventManager: $this->createMockEventManager(),
                categoryFactory: $this->createMockCategoryFactory(['789', '987', '444']),
                genreFactory: $this->createMockGenreFactory(['123', '456'], ['789', '987']),
                castMemberFactory: $this->createMockCastMemberFactory(),
            );
            $useCase->execute($this->createMockInput(
                id: $id,
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

        $id = Uuid::random();
        $entity = $this->getEntity($id);

        $mockRepo = $this->createMockRepository();
        $mockRepo->shouldReceive('findById')->andReturn($entity);

        $useCase = new UseCase(
            repository: $mockRepo,
            transaction: $this->getDatabaseTransactionInterface(timesCallRollback: 1),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );
        $useCase->execute($this->createMockInput(
            id: $id,
            categories: ['123', '456']
        ));
    }

    public function testExecExceptionGenres()
    {
        $this->expectException(GenreNotFound::class);
        $this->expectExceptionMessage('Genres not found');

        $id = Uuid::random();
        $entity = $this->getEntity($id);

        $mockRepo = $this->createMockRepository();
        $mockRepo->shouldReceive('findById')->andReturn($entity);

        $useCase = new UseCase(
            repository: $mockRepo,
            transaction: $this->getDatabaseTransactionInterface(timesCallRollback: 1),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );
        $useCase->execute($this->createMockInput(
            id: $id,
            genres: ['123', '456']
        ));
    }

    public function testExecExceptionCastMembers()
    {
        $this->expectException(CastMemberNotFound::class);
        $this->expectExceptionMessage('Cast Members not found');

        $id = Uuid::random();
        $entity = $this->getEntity($id);

        $mockRepo = $this->createMockRepository();
        $mockRepo->shouldReceive('findById')->andReturn($entity);

        $useCase = new UseCase(
            repository: $mockRepo,
            transaction: $this->getDatabaseTransactionInterface(timesCallRollback: 1),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryFactory: $this->createMockCategoryFactory(),
            genreFactory: $this->createMockGenreFactory(),
            castMemberFactory: $this->createMockCastMemberFactory(),
        );
        $useCase->execute($this->createMockInput(
            id: $id,
            castMembers: ['123', '456']
        ));
    }

    protected function createMockRepository($success = true)
    {
        $mock = Mockery::spy(stdClass::class, VideoRepositoryInterface::class);
        $mock->shouldReceive('update')->andReturn($success);

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
        string $id,
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
            $id,
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

    protected function getEntity(Uuid $id)
    {
        return new Video([
            'id' => $id,
            'title' => 'title',
            'description' => 'description',
            'yearLaunched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => Rating::L,
        ]);
    }
}
