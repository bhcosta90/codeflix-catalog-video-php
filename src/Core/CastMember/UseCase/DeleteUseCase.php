<?php

namespace Core\CastMember\UseCase;

use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Costa\DomainPackage\UseCase\Exception\NotFoundException;
use Costa\DomainPackage\UseCase\Exception\UseCaseException;
use Costa\DomainPackage\UseCase\DTO\Delete\{Input, Output};

class DeleteUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $repository)
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
