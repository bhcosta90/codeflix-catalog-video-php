<?php

namespace Core\CastMember\UseCase;

use Core\CastMember\Domain\Entity\CastMember;
use Core\CastMember\Domain\Enum\Type;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use Costa\DomainPackage\UseCase\Exception\UseCaseException;

class CreateUseCase
{
    public function __construct(protected CastMemberRepositoryInterface $repository)
    {
        //
    }

    public function execute(DTO\Create\Input $input): DTO\Create\Output
    {
        $entity = new CastMember(
            name: $input->name,
            type: Type::from($input->type),
        );

        if (! $input->is_active) {
            $entity->disabled();
        }

        if ($this->repository->insert($entity)) {
            return new DTO\Create\Output(
                id: $entity->id(),
                name: $entity->name,
                type: $entity->type->value,
                is_active: $entity->isActive,
                created_at: $entity->createdAt(),
            );
        }

        throw new UseCaseException(self::class);
    }
}
