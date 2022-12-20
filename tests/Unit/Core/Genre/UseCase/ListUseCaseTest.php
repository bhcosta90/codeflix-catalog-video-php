<?php

namespace Tests\Unit\Core\Genre\UseCase;

use Core\Genre\Domain\Entity\GenreEntity;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Genre\UseCase\{ListUseCase as UseCase, DTO\List\Output};
use DateTime;
use Shared\UseCase\DTO\List\Input;
use Shared\UseCase\Exception\NotFoundException;
use Shared\ValueObject\Uuid;
use Mockery;
use Tests\Unit\TestCase;

class ListUseCaseTest extends TestCase
{
    public function testExceptionListGenre()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('ID fake-id not found.');

        /** @var GenreRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn(null);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['fake-id']);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testListGenre()
    {
        $id = Uuid::random();
        /** @var GenreEntity|Mockery\MockInterface */
        $mockEntity = Mockery::spy(GenreEntity::class, ['test', true, $id]);
        $mockEntity->shouldReceive('id')->andReturn($id)
            ->shouldReceive('createdAt')->andReturn((new DateTime())->format('Y-m-d H:i:s'));

        /** @var GenreRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($mockEntity);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $retUseCase = $useCase->execute($mockInput);

        $this->assertInstanceOf(Output::class, $retUseCase);
        $this->assertNotEmpty($retUseCase->id);
        $this->assertNotEmpty($retUseCase->created_at);
        $this->assertEquals('test', $retUseCase->name);
        $this->assertTrue($retUseCase->is_active);
        $mockRepo->shouldHaveReceived('findById')->times(1);
    }
}
