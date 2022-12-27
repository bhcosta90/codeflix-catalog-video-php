<?php

namespace Tests\Unit\Core\CastMember\UseCase;

use Core\CastMember\Domain\Entity\CastMember;
use Core\CastMember\Domain\Enum\Type;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\CastMember\UseCase\{DeleteUseCase as UseCase};
use Costa\DomainPackage\UseCase\DTO\Delete\{Input, Output};
use Costa\DomainPackage\ValueObject\Uuid;
use Mockery;
use Costa\DomainPackage\UseCase\Exception\{NotFoundException, UseCaseException};
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
        /** @var CastMember|Mockery\MockInterface */
        $mockEntity = Mockery::spy(CastMember::class, ['test', Type::ACTOR, true, $id]);
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
        /** @var CastMember|Mockery\MockInterface */
        $mockEntity = Mockery::spy(CastMember::class, ['test', Type::ACTOR, true, $id]);
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
