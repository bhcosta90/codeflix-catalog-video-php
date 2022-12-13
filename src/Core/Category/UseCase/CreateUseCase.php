<?php

namespace Core\Category\UseCase;

use Core\Category\Domain\Entity\CategoryEntity;
use Core\Category\Domain\Repository\CategoryRepository;
use Core\Shared\UseCase\Exception\UseCaseException;

class CreateUseCase
{
    public function __construct(protected CategoryRepository $repository)
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
                active: $category->isActive,
            );
        }

        throw new UseCaseException(self::class);
    }
}


