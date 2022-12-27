<?php

namespace Core\Genre\UseCase;

use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Costa\DomainPackage\UseCase\DTO\List\Input;
use Costa\DomainPackage\UseCase\Exception\NotFoundException;

class ListUseCase
{
    public function __construct(protected GenreRepositoryInterface $repository)
    {
        //
    }

    public function execute(Input $input): DTO\List\Output
    {
        if ($entity = $this->repository->findById($input->id)) {
            return new DTO\List\Output(
                id: $entity->id(),
                name: $entity->name,
                is_active: $entity->isActive,
                created_at: $entity->createdAt(),
            );
        }

        throw new NotFoundException($input->id);
    }
}
