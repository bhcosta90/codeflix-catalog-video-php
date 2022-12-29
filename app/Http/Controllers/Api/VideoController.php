<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Video\StoreRequest;
use App\Http\Requests\Video\UpdateRequest;
use App\Http\Resources\VideoResource as Resource;
use Core\Video\Domain\Repository\VideoRepositoryFilter as Filter;
use Core\Video\UseCase;
use Core\Video\UseCase\DTO;
use Costa\DomainPackage\UseCase\DTO\Delete\Input as DeleteInput;
use Costa\DomainPackage\UseCase\DTO\List\Input as ListInput;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VideoController extends Controller
{
    public function index(Request $request, UseCase\PaginateUseCase $useCase)
    {
        $response = $useCase->execute(new DTO\Paginate\Input(
            page: (int) $request->get('page', 1),
            filter: new Filter(title: $request->get('title')),
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
            title: $request->title,
            description: $request->description,
            yearLaunched: $request->year_launched,
            duration: $request->duration,
            opened: $request->opened,
            rating: $request->rating,
            categories: $request->categories ?: [],
            genres: $request->genres ?: [],
            castMembers: $request->cast_members ?: [],
            videoFile: getArrayFile($request->video_file),
            trailerFile: getArrayFile($request->trailer_file),
            bannerFile: getArrayFile($request->banner_file),
            thumbFile: getArrayFile($request->thumb_file),
            thumbHalf: getArrayFile($request->thumb_half),
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
            title: $request->title,
            description: $request->description,
            yearLaunched: $request->year_launched,
            duration: $request->duration,
            opened: $request->opened,
            rating: $request->rating,
            categories: $request->categories ?: [],
            genres: $request->genres ?: [],
            castMembers: $request->cast_members ?: [],
            videoFile: getArrayFile($request->video_file),
            trailerFile: getArrayFile($request->trailer_file),
            bannerFile: getArrayFile($request->banner_file),
            thumbFile: getArrayFile($request->thumb_file),
            thumbHalf: getArrayFile($request->thumb_half),
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
