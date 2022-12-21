<?php

namespace App\Repositories\Eloquent;

use App\Models\CastMember;
use App\Repositories\Presenters\{ListPresenter, PaginatorPresenter};
use Core\CastMember\Domain\Entity\CastMemberEntity;
use Core\CastMember\Domain\Enum\Type;
use Core\CastMember\Domain\Repository\CastMemberRepositoryFilter;
use Core\CastMember\Domain\Repository\CastMemberRepositoryInterface;
use DateTime;
use Shared\Domain\Repository\Exceptions\DomainNotFoundException;
use Shared\Domain\Repository\ListInterface;
use Shared\Domain\Repository\PaginationInterface;
use Shared\ValueObject\Uuid;

class CastMemberRepositoryEloquent implements CastMemberRepositoryInterface
{
    public function __construct(private CastMember $model)
    {
        //
    }

    public function insert(CastMemberEntity $entity): bool
    {
        $this->model->create([
            'id' => $entity->id(),
            'name' => $entity->name,
            'type' => $entity->type->value,
            'is_active' => $entity->isActive,
            'created_at' => $entity->createdAt(),
        ]);

        return true;
    }

    public function update(CastMemberEntity $entity): bool
    {
        if ($obj = $this->model->find($entity->id())) {
            return (bool) $obj->update([
                'name' => $entity->name,
                'type' => $entity->type->value,
                'is_active' => $entity->isActive,
            ]);
        }

        throw new DomainNotFoundException("CastMember {$entity->id()} not found");
    }

    public function delete(string $id): bool
    {
        if ($obj = $this->model->find($id)) {
            return $obj->delete();
        }

        throw new DomainNotFoundException("CastMember {$id} not found");
    }

    public function findAll(CastMemberRepositoryFilter $filter = null): ListInterface
    {
        return new ListPresenter($this->filter($filter)->get());
    }

    public function findById(string $id): ?CastMemberEntity
    {
        if ($obj = $this->model->find($id)) {
            $response = new CastMemberEntity(
                name: $obj->name,
                type: Type::from($obj->type),
                id: new Uuid($obj->id),
                createdAt: new DateTime($obj->created_at),
            );

            ((bool) $obj->is_active) ? $response->enabled() : $response->disabled();

            return $response;
        }

        throw new DomainNotFoundException("CastMember {$id} not found");
    }

    public function paginate(
        CastMemberRepositoryFilter $filter = null,
        int $page = 1,
        int $total = 15
    ): PaginationInterface {
        return new PaginatorPresenter($this->filter($filter)->paginate());
    }

    private function filter(?CastMemberRepositoryFilter $filter)
    {
        $result = $this->model;

        if ($filter && ($filterResult = $filter->name) && !empty($filterResult)) {
            $result = $result->where('name', 'like', "%{$filterResult}%");
        }

        if ($filter && ($filterResult = $filter->type) && !empty($filterResult)) {
            $result = $result->where('type', $filterResult);
        }

        return $result->orderBy('name', 'asc');
    }
}
