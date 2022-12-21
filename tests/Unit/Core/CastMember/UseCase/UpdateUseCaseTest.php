<?php

namespace Tests\Unit\Core\CastMember\UseCase;

use Core\CastMember\Domain\Entity\CastMemberEntity;
use Core\CastMember\Domain\Enum\Type;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\CastMember\UseCase\{UpdateUseCase as UseCase, DTO\Update\Input, DTO\Update\Output};
use DateTime;
use Shared\ValueObject\Uuid;
use Mockery;
use Shared\UseCase\Exception\{NotFoundException, UseCaseException};
use Tests\Unit\TestCase;

class UpdateUseCaseTest extends TestCase
{
    public function testExceptionNotFoundUpdateCastMember()
    {
        $id = Uuid::random();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('ID ' . $id . ' not found.');

        /** @var CastMemberRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn(null);
        $mockRepo->shouldReceive('update')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id, 'test 2', 1, true]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testExceptionUseCaseUpdateCastMember()
    {
        $this->expectException(UseCaseException::class);
        $this->expectExceptionMessage('The class Core\CastMember\UseCase\UpdateUseCase is wrong.');

        $id = Uuid::random();
        /** @var CastMemberEntity|Mockery\MockInterface */
        $mockEntity = Mockery::spy(CastMemberEntity::class, ['test', Type::ACTOR, true, $id]);
        $mockEntity->shouldReceive('id')->andReturn($id);

        /** @var CastMemberRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepo->shouldReceive('update')->andReturn(false);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id, 'test 2', 2, true]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testUpdateCastMember()
    {
        $id = Uuid::random();
        /** @var CastMemberEntity|Mockery\MockInterface */
        $mockEntity = Mockery::spy(CastMemberEntity::class, ['test', Type::ACTOR, true, $id]);
        $mockEntity->shouldReceive('id')->andReturn($id)
            ->shouldReceive('createdAt')->andReturn((new DateTime())->format('Y-m-d H:i:s'));

        /** @var CastMemberRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepo->shouldReceive('update')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id, 'test 2', 1, true]);

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
