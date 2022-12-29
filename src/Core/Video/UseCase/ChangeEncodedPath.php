<?php

namespace Core\Video\UseCase;

use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Domain\ValueObject\Enum\Status;
use Core\Video\Domain\ValueObject\Media;
use Costa\DomainPackage\UseCase\Exception\NotFoundException;

class ChangeEncodedPath
{
    public function __construct(
        protected VideoRepositoryInterface $repository
    ) {
        //
    }

    public function execute(DTO\ChangeEncodedPath\Input $input): DTO\ChangeEncodedPath\Output
    {
        if ($entity = $this->repository->findById($input->id)) {
            $quantity = 0;
            if ($input->pathVideo) {
                $media = new Media(
                    path: $entity->videoFile?->path,
                    status: Status::COMPLETED,
                    encoded: $input->pathVideo,
                );
                $entity->setVideoFile($media);
                $quantity++;
            }

            if ($input->pathTrailer) {
                $media = new Media(
                    path: $entity->trailerFile?->path,
                    status: Status::COMPLETED,
                    encoded: $input->pathTrailer,
                );
                $entity->setTrailerFile($media);
                $quantity++;
            }

            if ($quantity) {
                $success = $this->repository->updateMedia($entity);
            }

            return new DTO\ChangeEncodedPath\Output(
                id: $entity->id(),
                pathVideo: $entity->videoFile?->encoded,
                pathTrailer: $entity->trailerFile?->encoded,
                success: $success ?? false,
                quantity: $quantity,
            );
        }

        throw new NotFoundException($input->id);
    }
}
