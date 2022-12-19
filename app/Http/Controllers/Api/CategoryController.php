<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Core\Category\Domain\Repository\CategoryRepositoryFilter;
use Core\Category\UseCase;
use Core\Category\UseCase\DTO;
use Illuminate\Http\Request;

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
}
