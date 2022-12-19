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
        if ($category = $this->repository->findById($input->id)) {
            $category->update(
                name: $input->name,
                description: $input->description,
            );

            $input->is_active ? $category->enabled() : $category->disabled();

            if ($this->repository->update($category)) {
                return new DTO\Update\Output(
                    id: $category->id(),
                    name: $category->name,
                    description: $category->description,
                    is_active: $category->isActive,
                    created_at: $category->createdAt(),
                );
            }
            throw new UseCaseException(self::class);
        }
        throw new NotFoundException($input->id);
    }
}
