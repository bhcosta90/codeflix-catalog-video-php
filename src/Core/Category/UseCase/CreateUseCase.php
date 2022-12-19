<?php

namespace Core\Category\UseCase;

use Core\Category\Domain\Entity\CategoryEntity;
use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Shared\UseCase\Exception\UseCaseException;

class CreateUseCase
{
    public function __construct(protected CategoryRepositoryInterface $repository)
    {
        //
    }

    public function execute(DTO\Create\Input $input): DTO\Create\Output
    {
        $category = new CategoryEntity(
            name: $input->name,
            description: $input->description,
        );

        if ($this->repository->insert($category)) {
            return new DTO\Create\Output(
                id: $category->id(),
                name: $category->name,
                description: $category->description,
                is_active: $category->isActive,
                created_at: $category->createdAt(),
            );
        }

        throw new UseCaseException(self::class);
    }
}


