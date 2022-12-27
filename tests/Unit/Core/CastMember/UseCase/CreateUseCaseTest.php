<?php

namespace Tests\Unit\Core\CastMember\UseCase;

use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Core\CastMember\UseCase\{CreateUseCase as UseCase, DTO\Create\Input, DTO\Create\Output};
use Costa\DomainPackage\UseCase\Exception\UseCaseException;
use Mockery;
use Tests\Unit\TestCase;

class CreateUseCaseTest extends TestCase
{
    public function testCreateNewCastMemberException()
    {
        $this->expectException(UseCaseException::class);
        $this->expectExceptionMessage('The class Core\CastMember\UseCase\CreateUseCase is wrong.');

        /** @var CastMemberRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepo->shouldReceive('insert')->andReturn(false);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['test', 1]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testCreateNewCastMember()
    {
        /** @var CastMemberRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepo->shouldReceive('insert')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['test', 1]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $retUseCase = $useCase->execute($mockInput);

        $this->assertInstanceOf(Output::class, $retUseCase);
        $this->assertNotEmpty($retUseCase->id);
        $this->assertNotEmpty($retUseCase->created_at);
        $this->assertEquals('test', $retUseCase->name);
        $this->assertEquals(1, $retUseCase->type);
        $this->assertTrue($retUseCase->is_active);
        $mockRepo->shouldHaveReceived('insert')->times(1);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['test', 1, false]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $retUseCase = $useCase->execute($mockInput);
        $this->assertFalse($retUseCase->is_active);
    }
}
