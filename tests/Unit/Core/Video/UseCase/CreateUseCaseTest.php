<?php

namespace Tests\Unit\Core\Video\UseCase;

use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Tests\Unit\TestCase;
use Core\Video\UseCase\CreateUseCase;
use Mockery;
use Shared\UseCase\Interfaces\FileStorageInterface;
use stdClass;
use Tests\Unit\Core\Video\Event\VideoEventManagerInterface;

class CreateUseCaseTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        new CreateUseCase(
            repository: $this->createMockRepository(),
            transaction: $this->getDatabaseTransactionInterface(),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
        );
        $this->assertTrue(true);
    }

    protected function createMockRepository()
    {
        return Mockery::spy(stdClass::class, VideoRepositoryInterface::class);
    }

    protected function createMockFileStorage()
    {
        return Mockery::spy(stdClass::class, FileStorageInterface::class);
    }

    protected function createMockEventManager()
    {
        return Mockery::spy(stdClass::class, VideoEventManagerInterface::class);
    }
}
