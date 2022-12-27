<?php

namespace Core\CastMember\UseCase;

use Core\CastMember\Domain\Enum\Type;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Costa\DomainPackage\UseCase\Exception\NotFoundException;
use Costa\DomainPackage\UseCase\Exception\UseCaseException;

class UpdateUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $repository)
    {
        //
    }

    public function execute(DTO\Update\Input $input): DTO\Update\Output
    {
        if ($entity = $this->repository->findById($input->id)) {
            $entity->update(
                name: $input->name,
                type: Type::from($input->type),
            );

            $input->is_active ? $entity->enabled() : $entity->disabled();

            if ($this->repository->update($entity)) {
                return new DTO\Update\Output(
                    id: $entity->id(),
                    name: $entity->name,
                    type: $entity->type->value,
                    is_active: $entity->isActive,
                    created_at: $entity->createdAt(),
                );
            }
            throw new UseCaseException(self::class);
        }

        throw new NotFoundException($input->id);
    }
}
