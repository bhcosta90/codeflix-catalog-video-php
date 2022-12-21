<?php

namespace Core\Category\UseCase;

use Core\Category\Domain\Entity\Category;
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
        $entity = new Category(
            name: $input->name,
            description: $input->description,
        );

        if (!$input->is_active) {
            $entity->disabled();
        }

        if ($this->repository->insert($entity)) {
            return new DTO\Create\Output(
                id: $entity->id(),
                name: $entity->name,
                description: $entity->description,
                is_active: $entity->isActive,
                created_at: $entity->createdAt(),
            );
        }

        throw new UseCaseException(self::class);
    }
}


