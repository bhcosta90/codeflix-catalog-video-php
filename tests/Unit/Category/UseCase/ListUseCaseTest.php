<?php

namespace Tests\Unit\Category\UseCase;

use Core\Category\Domain\Entity\CategoryEntity;
use Core\Category\Domain\Repository\CategoryRepository;
use Core\Category\UseCase\{ListUseCase as UseCase, DTO\List\Input, DTO\List\Output};
use Shared\UseCase\Exception\NotFoundException;
use Shared\ValueObject\Uuid;
use Mockery;
use Tests\Unit\TestCase;

class ListUseCaseTest extends TestCase
{
    public function testExceptionCreateNewCategory()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('ID fake-id not found.');
        
        /** @var CategoryRepository|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepository::class);
        $mockRepo->shouldReceive('findById')->andReturn(null);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, ['fake-id']);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $useCase->execute($mockInput);
    }
    
    public function testCreateNewCategory()
    {
        $id = Uuid::random();
        /** @var CategoryEntity|Mockery\MockInterface */
        $mockEntity = Mockery::spy(CategoryEntity::class, ['test', 'test', true, $id]);
        $mockEntity->shouldReceive('id')->andReturn($id);

        /** @var CategoryRepository|Mockery\MockInterface */
        $mockRepo = Mockery::spy(stdClass::class, CategoryRepository::class);
        $mockRepo->shouldReceive('findById')->andReturn($mockEntity);

        /** @var Input|Mockery\MockInterface */
        $mockInput = Mockery::mock(Input::class, [$id]);

        $useCase = new UseCase(
            repository: $mockRepo,
        );

        $retUseCase = $useCase->execute($mockInput);

        $this->assertInstanceOf(Output::class, $retUseCase);
        $this->assertNotEmpty($retUseCase->id);
        $this->assertEquals('test', $retUseCase->name);
        $this->assertEquals('test', $retUseCase->description);
        $this->assertTrue($retUseCase->active);
        $mockRepo->shouldHaveReceived('findById')->times(1);
    }
}
