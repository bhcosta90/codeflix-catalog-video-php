<?php

namespace Core\Category\UseCase;

use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Costa\DomainPackage\UseCase\DTO\List\Input;
use Costa\DomainPackage\UseCase\Exception\NotFoundException;

class ListUseCase
{
    public function __construct(protected CategoryRepositoryInterface $repository)
    {
        //
    }

    public function execute(Input $input): DTO\List\Output
    {
        if ($entity = $this->repository->findById($input->id)) {
            return new DTO\List\Output(
                id: $entity->id(),
                name: $entity->name,
                description: $entity->description,
                is_active: $entity->isActive,
                created_at: $entity->createdAt(),
            );
        }

        throw new NotFoundException($input->id);
    }
}
