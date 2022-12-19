<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Core\Category\Domain\Repository\CategoryRepositoryFilter;
use Core\Category\UseCase;
use Core\Category\UseCase\DTO;
use Illuminate\Http\Request;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;
use Illuminate\Http\Response;
use Shared\UseCase\DTO\List\Input as ListInput;
use Shared\UseCase\DTO\Delete\Input as DeleteInput;

class CategoryController extends Controller
{
    public function index(Request $request, UseCase\PaginateUseCase $useCase)
    {
        $response = $useCase->execute(new DTO\Paginate\Input(
            page: (int) $request->get('page', 1),
            filter: new CategoryRepositoryFilter(name: $request->get('name')),
        ));

        return CategoryResource::collection(collect($response->items))
            ->additional([
                'meta' => [
                    'total' => $response->total,
                    'first_page' => $response->first_page,
                    'last_page' => $response->last_page,
                    'to' => $response->to,
                    'from' => $response->from,
                    'per_page' => $response->per_page,
                ]
            ]);
    }

    public function store(StoreRequest $request, UseCase\CreateUseCase $useCase)
    {
        $response = $useCase->execute(new DTO\Create\Input(
            name: $request->name,
            description: $request->description,
        ));

        return (new CategoryResource(collect($response)))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(UseCase\ListUseCase $useCase, string $id)
    {
        $response = $useCase->execute(new ListInput(id: $id));
        return (new CategoryResource(collect($response)))->response();
    }

    public function update(UpdateRequest $request, UseCase\UpdateUseCase $useCase, string $id)
    {
        $response = $useCase->execute(new DTO\Update\Input(
            id: $id,
            name: $request->name,
            description: $request->description,
            is_active: $request->is_active,
        ));

        return (new CategoryResource(collect($response)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(UseCase\DeleteUseCase $useCase, string $id){
        $useCase->execute(new DeleteInput(
            id: $id,
        ));
        return response()->noContent();
    }
}
