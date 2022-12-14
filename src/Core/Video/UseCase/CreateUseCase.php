<?php

namespace Core\Video\UseCase;

use Core\Video\Builder\VideoCreateBuilder;
use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Event\VideoCreatedEvent;
use Core\Video\Interfaces\VideoBuilderInterface;
use Costa\DomainPackage\UseCase\Exception\UseCaseException;
use Throwable;

class CreateUseCase extends BaseUseCase
{
    public function builder(): VideoBuilderInterface
    {
        return new VideoCreateBuilder();
    }

    public function execute(DTO\Create\Input $input): DTO\Create\Output
    {
        try {
            $this->builder->createEntity($input);
            $this->verifyCategories($input);
            $this->verifyGenres($input);
            $this->verifyCastMembers($input);

            if ($this->repository->insert($this->builder->getEntity())) {
                $filesUploads = $this->storageAllFiles($input);
                $this->repository->updateMedia($this->builder->getEntity());
                $this->eventManager->dispatch(new VideoCreatedEvent($this->builder->getEntity()));
                $this->transaction->commit();

                return $this->output($this->builder->getEntity());
            }
        } catch (Throwable $e) {
            $this->transaction->rollback();
            if (isset($filesUploads)) {
                foreach ($filesUploads as $file) {
                    $this->storage->delete($file);
                }
            }

            throw $e;
        }

        throw new UseCaseException(self::class);
    }

    protected function output(Video $entity)
    {
        return new DTO\Create\Output(
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
