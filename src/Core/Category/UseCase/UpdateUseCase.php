<?php

namespace Core\Category\UseCase;

use Core\Category\Domain\Repository\CategoryRepository;
use Shared\UseCase\Exception\NotFoundException;
use Shared\UseCase\Exception\UseCaseException;

class UpdateUseCase
{
    public function __construct(protected CategoryRepository $repository)
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

            if ($this->repository->update($category)) {
                return new DTO\Update\Output(
                    id: $category->id(),
                    name: $category->name,
                    description: $category->description,
                    active: $category->isActive,
                );
            }
            throw new UseCaseException(self::class);
        }
        throw new NotFoundException($input->id);
    }
}
