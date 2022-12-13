<?php

namespace Core\Category\UseCase;

use Core\Category\Domain\Repository\CategoryRepository;
use Shared\UseCase\Exception\NotFoundException;
use Shared\UseCase\Exception\UseCaseException;

class DeleteUseCase
{
    public function __construct(protected CategoryRepository $repository)
    {
        //
    }

    public function execute(DTO\Delete\Input $input): DTO\Delete\Output
    {
        if ($category = $this->repository->findById($input->id)) {
            if ($this->repository->delete($category)) {
                return new DTO\Delete\Output(
                    success: true,
                );
            }
            throw new UseCaseException(self::class);
        }
        throw new NotFoundException($input->id);
    }
}
