<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Genre\StoreRequest;
use App\Http\Requests\Genre\UpdateRequest;
use App\Http\Resources\GenreResource as Resource;
use Core\Genre\Domain\Repository\GenreRepositoryFilter as Filter;
use Core\Genre\UseCase;
use Core\Genre\UseCase\DTO;
use Costa\DomainPackage\UseCase\DTO\Delete\Input as DeleteInput;
use Costa\DomainPackage\UseCase\DTO\List\Input as ListInput;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GenreController extends Controller
{
    public function index(Request $request, UseCase\PaginateUseCase $useCase)
    {
        $response = $useCase->execute(new DTO\Paginate\Input(
            page: (int) $request->get('page', 1),
            filter: new Filter(name: $request->get('name'), categories: $request->get('categories')),
        ));

        return Resource::collection(collect($response->items))
            ->additional([
                'meta' => [
                    'total' => $response->total,
                    'first_page' => $response->first_page,
                    'last_page' => $response->last_page,
                    'current_page' => $response->current_page,
                    'to' => $response->to,
                    'from' => $response->from,
                    'per_page' => $response->per_page,
                ],
            ]);
    }

    public function store(StoreRequest $request, UseCase\CreateUseCase $useCase)
    {
        $response = $useCase->execute(new DTO\Create\Input(
            name: $request->name,
            categories: $request->categories ?? [],
            is_active: $request->is_active ?? true
        ));

        return (new Resource($response))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(UseCase\ListUseCase $useCase, string $id)
    {
        $response = $useCase->execute(new ListInput(id: $id));

        return (new Resource($response))->response();
    }

    public function update(UpdateRequest $request, UseCase\UpdateUseCase $useCase, string $id)
    {
        $response = $useCase->execute(new DTO\Update\Input(
            id: $id,
            name: $request->name,
            categories: $request->categories ?? [],
            is_active: $request->is_active,
        ));

        return (new Resource($response))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(UseCase\DeleteUseCase $useCase, string $id)
    {
        $useCase->execute(new DeleteInput(
            id: $id,
        ));

        return response()->noContent();
    }
}
