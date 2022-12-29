<?php

namespace Tests\Unit\Core\Video\UseCase;

use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\UseCase\{DeleteUseCase as UseCase};
use Costa\DomainPackage\UseCase\DTO\Delete\Input;
use Costa\DomainPackage\UseCase\DTO\Delete\Output;
use Costa\DomainPackage\UseCase\Exception\NotFoundException;
use Costa\DomainPackage\UseCase\Exception\UseCaseException;
use Costa\DomainPackage\ValueObject\Uuid;
use Mockery;
use Tests\Unit\TestCase;

class DeleteUseCaseTest extends TestCase
{
    public function testExceptionNotFoundDeleteVideo()
    {
        $id = Uuid::random();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('ID '.$id.' not found.');

        /** @var VideoRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, VideoRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn(null);
        $mockRepo->shouldReceive('delete')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id, 'test 2', 'test 3']);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testExceptionUseCaseDeleteVideo()
    {
        $this->expectException(UseCaseException::class);
        $this->expectExceptionMessage('The class Core\Video\UseCase\DeleteUseCase is wrong.');

        $id = Uuid::random();
        $entity = new Video([
            'id' => $id,
            'title' => 'title',
            'description' => 'description',
            'yearLaunched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => Rating::L,
        ]);

        /** @var VideoRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, VideoRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($entity);
        $mockRepo->shouldReceive('delete')->andReturn(false);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testDeleteVideo()
    {
        $id = Uuid::random();
        $entity = new Video([
            'id' => $id,
            'title' => 'title',
            'description' => 'description',
            'yearLaunched' => 2020,
            'duration' => 50,
            'opened' => true,
            'rating' => Rating::L,
        ]);

        /** @var VideoRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, VideoRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($entity);
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
