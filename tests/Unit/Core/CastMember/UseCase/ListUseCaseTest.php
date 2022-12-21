<?php

namespace Tests\Unit\Core\CastMember\UseCase;

use Core\CastMember\Domain\Entity\CastMemberEntity;
use Core\CastMember\Domain\Enum\Type;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\CastMember\UseCase\{ListUseCase as UseCase, DTO\List\Output};
use DateTime;
use Shared\UseCase\DTO\List\Input;
use Shared\UseCase\Exception\NotFoundException;
use Shared\ValueObject\Uuid;
use Mockery;
use Tests\Unit\TestCase;

class ListUseCaseTest extends TestCase
{
    public function testExceptionListCastMember()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('ID fake-id not found.');

        /** @var CastMemberRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn(null);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['fake-id']);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testListCastMember()
    {
        $id = Uuid::random();
        /** @var CastMemberEntity|Mockery\MockInterface */
        $mockEntity = Mockery::spy(CastMemberEntity::class, ['test', Type::ACTOR, true, $id]);
        $mockEntity->shouldReceive('id')->andReturn($id)
            ->shouldReceive('createdAt')->andReturn((new DateTime())->format('Y-m-d H:i:s'));

        /** @var CastMemberRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
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
        $this->assertEquals(2, $retUseCase->type);
        $this->assertTrue($retUseCase->is_active);
        $mockRepo->shouldHaveReceived('findById')->times(1);
    }
}
