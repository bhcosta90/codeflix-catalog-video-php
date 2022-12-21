<?php

namespace Tests\Unit\Core\CastMember\UseCase;

use Core\CastMember\Domain\Entity\CastMemberEntity;
use Core\CastMember\Domain\Enum\Type;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\CastMember\UseCase\{DeleteUseCase as UseCase};
use Shared\UseCase\DTO\Delete\{Input, Output};
use Shared\ValueObject\Uuid;
use Mockery;
use Shared\UseCase\Exception\{NotFoundException, UseCaseException};
use Tests\Unit\TestCase;

class DeleteUseCaseTest extends TestCase
{
    public function testExceptionNotFoundDeleteCastMember()
    {
        $id = Uuid::random();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('ID ' . $id . ' not found.');

        /** @var CastMemberRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn(null);
        $mockRepo->shouldReceive('delete')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id, 'test 2', 'test 3']);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testExceptionUseCaseDeleteCastMember()
    {
        $this->expectException(UseCaseException::class);
        $this->expectExceptionMessage('The class Core\CastMember\UseCase\DeleteUseCase is wrong.');

        $id = Uuid::random();
        /** @var CastMemberEntity|Mockery\MockInterface */
        $mockEntity = Mockery::spy(CastMemberEntity::class, ['test', Type::ACTOR, true, $id]);
        $mockEntity->shouldReceive('id')->andReturn($id);

        /** @var CastMemberRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepo->shouldReceive('delete')->andReturn(false);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testDeleteCastMember()
    {
        $id = Uuid::random();
        /** @var CastMemberEntity|Mockery\MockInterface */
        $mockEntity = Mockery::spy(CastMemberEntity::class, ['test', 'test', true, $id]);
        $mockEntity->shouldReceive('id')->andReturn($id);

        /** @var CastMemberRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
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
