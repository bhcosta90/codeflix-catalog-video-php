<?php

namespace Core\Genre\UseCase;

use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Shared\UseCase\Exception\NotFoundException;
use Shared\UseCase\Exception\UseCaseException;
use Shared\UseCase\DTO\Delete\{Input, Output};

class DeleteUseCase
{
    public function __construct(protected GenreRepositoryInterface $repository)
    {
        //
    }

    public function execute(Input $input): Output
    {
        if ($entity = $this->repository->findById($input->id)) {
            if ($this->repository->delete($entity->id())) {
                return new Output(
                    success: true,
                );
            }
            throw new UseCaseException(self::class);
        }
        throw new NotFoundException($input->id);
    }
}
