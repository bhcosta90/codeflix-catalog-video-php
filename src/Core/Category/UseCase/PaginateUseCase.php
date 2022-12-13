<?php

namespace Core\Category\UseCase;

use Core\Category\Domain\Repository\CategoryRepository;
use Shared\UseCase\Exception\UseCaseException;

class PaginateUseCase
{
    public function __construct(protected CategoryRepository $repository)
    {
        //
    }

    public function execute(DTO\Paginate\Input $input): DTO\Paginate\Output
    {
        $category = $this->repository->paginate($input->page);

        return new DTO\Paginate\Output(
            items: $category->items(),
            total: $category->total(),
            per_page: $category->perPage(),
            first_page: $category->firstPage(),
            last_page: $category->lastPage(),
            to: $category->to(),
            from: $category->from(),
        );
    }
}
