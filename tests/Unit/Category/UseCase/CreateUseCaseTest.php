<?php

use Core\Category\Domain\Repository\CategoryRepository;
use Core\Category\UseCase\{CreateUseCase, DTO\Create\Input, DTO\Create\Output};
use Core\Shared\UseCase\Exception\UseCaseException;
use PHPUnit\Framework\TestCase;

class CreateUseCaseTest extends TestCase
{
    public function testCreateNewCategoryException()
    {
        $this->expectException(UseCaseException::class);
        $this->expectExceptionMessage('The class Core\Category\UseCase\CreateUseCase is wrong.');

        /** @var CategoryRepository|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepository::class);
        $mockRepo->shouldReceive('insert')->andReturn(false);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['test', 'test2']);

        $useCase = new CreateUseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }

    public function testCreateNewCategory()
    {
        /** @var CategoryRepository|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepository::class);
        $mockRepo->shouldReceive('insert')->andReturn(true);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['test', 'test2']);

        $useCase = new CreateUseCase(
            repository: $mockRepo,
        );

        $retUseCase = $useCase->execute($mockInput);

        $this->assertInstanceOf(Output::class, $retUseCase);
        $this->assertNotEmpty($retUseCase->id);
        $this->assertEquals('test', $retUseCase->name);
        $this->assertEquals('test2', $retUseCase->description);
        $this->assertTrue($retUseCase->active);
        $mockRepo->shouldHaveReceived('insert')->times(1);
    }
}
