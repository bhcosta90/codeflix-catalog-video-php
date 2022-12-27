<?php

namespace Tests\Unit\Core\Category\UseCase;

use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Core\Category\UseCase\{CreateUseCase as UseCase, DTO\Create\Input, DTO\Create\Output};
use Costa\DomainPackage\UseCase\Exception\UseCaseException;
use Mockery;
use Tests\Unit\TestCase;

class CreateUseCaseTest extends TestCase
{
    public function testCreateNewCategoryException()
    {
        $this->expectException(UseCaseException::class);
        $this->expectExceptionMessage('The class Core\Category\UseCase\CreateUseCase is wrong.');

        /** @var CategoryRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('insert')->andReturn(false);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['test', 'test2']);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testCreateNewCategory()
    {
        /** @var CategoryRepositoryInterface|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('insert')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['test', 'test2']);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $retUseCase = $useCase->execute($mockInput);

        $this->assertInstanceOf(Output::class, $retUseCase);
        $this->assertNotEmpty($retUseCase->id);
        $this->assertNotEmpty($retUseCase->created_at);
        $this->assertEquals('test', $retUseCase->name);
        $this->assertEquals('test2', $retUseCase->description);
        $this->assertTrue($retUseCase->is_active);
        $mockRepo->shouldHaveReceived('insert')->times(1);
    }
}
