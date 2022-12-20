<?php

namespace Core\Category\UseCase;

use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Shared\UseCase\Exception\NotFoundException;
use Shared\UseCase\Exception\UseCaseException;

class UpdateUseCase
{
    public function __construct(protected CategoryRepositoryInterface $repository)
    {
        //
    }

    public function execute(DTO\Update\Input $input): DTO\Update\Output
    {
        if ($entity = $this->repository->findById($input->id)) {
            $entity->update(
                name: $input->name,
                description: $input->description,
            );

            $input->is_active ? $entity->enabled() : $entity->disabled();

            if ($this->repository->update($entity)) {
                return new DTO\Update\Output(
                    id: $entity->id(),
                    name: $entity->name,
                    description: $entity->description,
                    is_active: $entity->isActive,
                    created_at: $entity->createdAt(),
                );
            }
            throw new UseCaseException(self::class);
        }
        throw new NotFoundException($input->id);
    }
}
