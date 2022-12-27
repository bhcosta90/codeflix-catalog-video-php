<?php

namespace Core\Video\UseCase;

use Core\Video\Builder\VideoUpdateBuilder;
use Core\Video\Domain\Entity\Video;
use Core\Video\Interfaces\VideoBuilderInterface;
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
        try {
            $this->builder->createEntity($input);
            $this->createEntity($input);

            if ($this->repository->update($this->builder->getEntity())) {
                $filesUploads = $this->storageAllFiles($input);
                $this->repository->updateMedia($this->builder->getEntity());
                $this->eventManager->dispatch($this->builder->getEntity());
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
        return new DTO\Update\Output(
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
}
