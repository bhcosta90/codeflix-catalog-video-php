<?php

namespace Core\Genre\UseCase;

use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Costa\DomainPackage\UseCase\DTO\Paginate\Output;

class PaginateUseCase
{
    public function __construct(protected GenreRepositoryInterface $repository)
    {
        //
    }

    public function execute(DTO\Paginate\Input $input): Output
    {
        $result = $this->repository->paginate($input->filter, $input->page);

        return new Output(
            items: $result->items(),
            total: $result->total(),
            current_page: $result->currentPage(),
            per_page: $result->perPage(),
            first_page: $result->firstPage(),
            last_page: $result->lastPage(),
            to: $result->to(),
            from: $result->from(),
        );
    }
}
