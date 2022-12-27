<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CastMemberResource;
use Core\CastMember\Domain\Repository\CastMemberRepositoryFilter;
use Core\CastMember\UseCase;
use Core\CastMember\UseCase\DTO;
use Illuminate\Http\Request;
use App\Http\Requests\CastMember\StoreRequest;
use App\Http\Requests\CastMember\UpdateRequest;
use Illuminate\Http\Response;
use Costa\DomainPackage\UseCase\DTO\List\Input as ListInput;
use Costa\DomainPackage\UseCase\DTO\Delete\Input as DeleteInput;

class CastMemberController extends Controller
{
    public function index(Request $request, UseCase\PaginateUseCase $useCase)
    {
        $response = $useCase->execute(new DTO\Paginate\Input(
            page: (int) $request->get('page', 1),
            filter: new CastMemberRepositoryFilter(name: $request->get('name'), type: $request->type),
        ));

        return CastMemberResource::collection(collect($response->items))
            ->additional([
                'meta' => [
                    'total' => $response->total,
                    'first_page' => $response->first_page,
                    'last_page' => $response->last_page,
                    'current_page' => $response->current_page,
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
            type: $request->type,
            is_active: $request->is_active ?? true
        ));

        return (new CastMemberResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(UseCase\ListUseCase $useCase, string $id)
    {
        $response = $useCase->execute(new ListInput(id: $id));
        return (new CastMemberResource($response))->response();
    }

    public function update(UpdateRequest $request, UseCase\UpdateUseCase $useCase, string $id)
    {
        $response = $useCase->execute(new DTO\Update\Input(
            id: $id,
            name: $request->name,
            type: $request->type,
            is_active: $request->is_active,
        ));

        return (new CastMemberResource($response))
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
