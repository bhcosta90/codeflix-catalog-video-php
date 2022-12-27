<?php

namespace Core\Video\UseCase;

use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Costa\DomainPackage\UseCase\DTO\List\Input;
use Costa\DomainPackage\UseCase\Exception\NotFoundException;

class ListUseCase
{
    public function __construct(protected VideoRepositoryInterface $repository)
    {
        //
    }

    public function execute(Input $input): DTO\List\Output
    {
        if ($entity = $this->repository->findById($input->id)) {
            return new DTO\List\Output(
                id: $entity->id(),
                title: $entity->title,
                description: $entity->description,
                yearLaunched: $entity->yearLaunched,
                duration: $entity->duration,
                opened: $entity->opened,
                rating: $entity->rating->value,
                created_at: $entity->createdAt(),
                categories: $entity->categories,
                genres: $entity->genres,
                castMembers: $entity->castMembers,
                thumbFile: $entity->thumbFile?->path,
                thumbHalf: $entity->thumbHalf?->path,
                bannerFile: $entity->bannerFile?->path,
                trailerFile: $entity->trailerFile?->path,
                videoFile: $entity->videoFile?->path,
            );
        }

        throw new NotFoundException($input->id);
    }
}
