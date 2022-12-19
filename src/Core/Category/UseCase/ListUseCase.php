<?php

namespace Core\Category\UseCase;

use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Shared\UseCase\DTO\List\Input;
use Shared\UseCase\Exception\NotFoundException;

class ListUseCase
{
    public function __construct(protected CategoryRepositoryInterface $repository)
    {
        //
    }

    public function execute(Input $input): DTO\List\Output
    {
        if ($category = $this->repository->findById($input->id)) {
            return new DTO\List\Output(
                id: $category->id(),
                name: $category->name,
                description: $category->description,
                is_active: $category->isActive,
                created_at: $category->createdAt(),
            );
        }

        throw new NotFoundException($input->id);
    }
}
