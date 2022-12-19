<?php

namespace Core\Category\UseCase;

use Core\Category\Domain\Repository\CategoryRepositoryInterface;
use Shared\UseCase\DTO\Paginate\Output;

class PaginateUseCase
{
    public function __construct(protected CategoryRepositoryInterface $repository)
    {
        //
    }

    public function execute(DTO\Paginate\Input $input): Output
    {
        $category = $this->repository->paginate(null, $input->page);

        return new Output(
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
