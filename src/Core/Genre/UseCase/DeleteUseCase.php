<?php

namespace Core\Genre\UseCase;

use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Costa\DomainPackage\UseCase\DTO\Delete\Input;
use Costa\DomainPackage\UseCase\DTO\Delete\Output;
use Costa\DomainPackage\UseCase\Exception\NotFoundException;
use Costa\DomainPackage\UseCase\Exception\UseCaseException;

class DeleteUseCase
{
    public function __construct(protected GenreRepositoryInterface $repository)
    {
        //
    }

    public function execute(Input $input): Output
    {
        if ($entity = $this->repository->findById($input->id)) {
            if ($this->repository->delete($entity->id())) {
                return new Output(
                    success: true,
                );
            }
            throw new UseCaseException(self::class);
        }
        throw new NotFoundException($input->id);
    }
}
