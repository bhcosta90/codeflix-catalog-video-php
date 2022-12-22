<?php

namespace Core\Video\UseCase;

use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Shared\UseCase\Interfaces\{DatabaseTransactionInterface, FileStorageInterface};
use Tests\Unit\Core\Video\Event\VideoEventManagerInterface;

class CreateUseCase
{
    public function __construct(
        protected VideoRepositoryInterface $repository,
        protected DatabaseTransactionInterface $transaction,
        protected FileStorageInterface $storage,
        protected VideoEventManagerInterface $eventManager,
    ) {
        //
    }
}
