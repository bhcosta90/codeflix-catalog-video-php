<?php

namespace Core\Video\UseCase;

use Core\Video\Builder\VideoUpdateBuilder;
use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\Event\VideoCreatedEvent;
use Core\Video\Interfaces\VideoBuilderInterface;
use Costa\DomainPackage\UseCase\Exception\NotFoundException;
use Costa\DomainPackage\UseCase\Exception\UseCaseException;
use Throwable;

class UpdateUseCase extends BaseUseCase
{
    public function builder(): VideoBuilderInterface
    {
        return new VideoUpdateBuilder();
    }

    public function execute(DTO\Update\Input $input): DTO\Update\Output
    {
        if ($obj = $this->repository->findById($input->id)) {
            try {
                $this->builder->createEntity($obj);
                $entity = $this->builder->getEntity();
                $entity->update([
                    'title' => $input->title,
                    'description' => $input->description,
                    'yearLaunched' => $input->yearLaunched,
                    'duration' => $input->duration,
                    'opened' => $input->opened,
                    'rating' => Rating::from($input->rating),
                    'categories' => $input->categories,
                    'genres' => $input->genres,
                    'castMembers' => $input->castMembers,
                ]);

                $this->verifyCategories($input);
                $this->verifyGenres($input);
                $this->verifyCastMembers($input);
                if ($this->repository->update($entity)) {
                    $filesUploads = $this->storageAllFiles($input);
                    $this->repository->updateMedia($entity);
                    $this->eventManager->dispatch(new VideoCreatedEvent($entity));
                    $this->transaction->commit();

                    return $this->output($entity);
                }

                throw new UseCaseException(self::class);
            } catch (Throwable $e) {
                $this->transaction->rollback();
                if (isset($filesUploads)) {
                    foreach ($filesUploads as $file) {
                        $this->storage->delete($file);
                    }
                }

                throw $e;
            }
        }
        throw new NotFoundException($input->id);
    }

    protected function output(Video $entity)
    {
        return new DTO\Update\Output(
            id: $entity->id(),
            title: $entity->title,
            description: $entity->description,
            year_launched: $entity->yearLaunched,
            duration: $entity->duration,
            opened: $entity->opened,
            rating: $entity->rating->value,
            created_at: $entity->createdAt(),
            categories: $entity->categories,
            genres: $entity->genres,
            cast_members: $entity->castMembers,
            thumb_file: $entity->thumbFile?->path,
            thumb_half: $entity->thumbHalf?->path,
            banner_file: $entity->bannerFile?->path,
            trailer_file: $entity->trailerFile?->path,
            video_file: $entity->videoFile?->path,
        );
    }
}
