<?php

namespace Core\Category\UseCase;

use Core\Category\Domain\Repository\CategoryRepository;
use Core\Shared\UseCase\Exception\NotFoundException;

class ListUseCase
{
    public function __construct(protected CategoryRepository $repository)
    {
        //
    }

    public function execute(DTO\List\Input $input): DTO\List\Output
    {
        if ($category = $this->repository->findById($input->id)) {
            return new DTO\List\Output(
                id: $category->id(),
                name: $category->name,
                description: $category->description,
                active: $category->isActive,
            );
        }

        throw new NotFoundException($input->id);
    }
}
