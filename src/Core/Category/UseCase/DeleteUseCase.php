<?php

namespace Core\Category\UseCase;

use Core\Category\Domain\Repository\CategoryRepository;
use Shared\UseCase\Exception\NotFoundException;
use Shared\UseCase\Exception\UseCaseException;
use Shared\UseCase\DTO\Delete\{Input, Output};

class DeleteUseCase
{
    public function __construct(protected CategoryRepository $repository)
    {
        //
    }

    public function execute(Input $input): Output
    {
        if ($category = $this->repository->findById($input->id)) {
            if ($this->repository->delete($category)) {
                return new Output(
                    success: true,
                );
            }
            throw new UseCaseException(self::class);
        }
        throw new NotFoundException($input->id);
    }
}
