<?php

namespace Tests\Unit\Core\Video\UseCase;

use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\UseCase\DTO\List\Output;
use Core\Video\UseCase\ListUseCase as UseCase;
use Costa\DomainPackage\UseCase\DTO\List\Input;
use Costa\DomainPackage\UseCase\Exception\NotFoundException;
use Costa\DomainPackage\ValueObject\Uuid;
use Mockery;
use Tests\Unit\TestCase;

class ListUseCaseTest extends TestCase
{
    public function testExceptionListVideo()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('ID fake-id not found.');

        /** @var VideoRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, VideoRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn(null);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['fake-id']);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testListVideo()
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

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $retUseCase = $useCase->execute($mockInput);

        $this->assertInstanceOf(Output::class, $retUseCase);
        $this->assertNotEmpty($retUseCase->id);
        $this->assertNotEmpty($retUseCase->created_at);
        $this->assertEquals('title', $retUseCase->title);
        $mockRepo->shouldHaveReceived('findById')->times(1);
    }
}
