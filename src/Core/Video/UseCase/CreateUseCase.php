<?php

namespace Core\Video\UseCase;

use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;
use Core\Video\Factory\{CastMemberFactoryInterface, CategoryFactoryInterface, GenreFactoryInterface};
use Shared\UseCase\Exception\UseCaseException;
use Shared\UseCase\Interfaces\{DatabaseTransactionInterface, FileStorageInterface};
use Tests\Unit\Core\Video\Event\VideoEventManagerInterface;

class CreateUseCase
{
    protected Video $entity;

    public function __construct(
        protected VideoRepositoryInterface $repository,
        protected DatabaseTransactionInterface $transaction,
        protected FileStorageInterface $storage,
        protected VideoEventManagerInterface $eventManager,
        protected CategoryFactoryInterface $categoryFactory,
        protected GenreFactoryInterface $genreFactory,
        protected CastMemberFactoryInterface $castMemberFactory,
    ) {
        //
    }

    public function execute(DTO\Create\Input $input): DTO\Create\Output
    {
        try {
            $this->entity = $this->createEntity($input);

            if ($this->repository->insert($this->entity)) {
                $filesUploads = $this->storageAllFiles($input);
                $this->repository->updateMedia($this->entity);
                $this->eventManager->dispatch($this->entity);
                $this->transaction->commit();

                return $this->output($this->entity);
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

    protected function createEntity(DTO\Create\Input $input): Video
    {
        $entity = new Video(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: true,
            rating: $input->rating,
            categories: $input->categories,
            genres: $input->genres,
            castMembers: $input->castMembers,
        );

        $this->verifyCategories($input);
        $this->verifyGenres($input);
        $this->verifyCastMembers($input);

        return $entity;
    }

    protected function storageAllFiles(DTO\Create\Input $input): array
    {
        $result = [];

        if ($path = $this->storeMedia($this->entity->id(), $input->videoFile)) {
            $media = new Media(
                path: $path
            );
            array_push($result);
            $this->entity->setVideoFile($media);
        }

        if ($path = $this->storeMedia($this->entity->id(), $input->trailerFile)) {
            $media = new Media(
                path: $path
            );
            array_push($result);
            $this->entity->setTrailerFile($media);
        }

        if ($path = $this->storeMedia($this->entity->id(), $input->bannerFile)) {
            $media = new Image(
                path: $path
            );
            array_push($result);
            $this->entity->setBannerFile($media);
        }

        if ($path = $this->storeMedia($this->entity->id(), $input->thumbFile)) {
            $media = new Image(
                path: $path
            );
            array_push($result);
            $this->entity->setThumbFile($media);
        }

        if ($path = $this->storeMedia($this->entity->id(), $input->thumbHalf)) {
            $media = new Image(
                path: $path
            );
            array_push($result);
            $this->entity->setThumbHalf($media);
        }

        return $result;
    }

    protected function storeMedia(string $path, ?array $media = null): ?string
    {
        if ($media) {
            return $this->storage->store(
                path: $path,
                file: $media,
            );
        }

        return null;
    }

    protected function verifyCategories(DTO\Create\Input $input)
    {
        if ($input->categories) {
            $categoriesDb = $this->categoryFactory->findByIds($input->categories);
            $categoriesDiff = array_diff($input->categories, $categoriesDb);
            if ($categoriesDiff) {
                throw new Exceptions\CategoryNotFound('Categories not found', $categoriesDiff);
            }
        }
    }

    protected function verifyGenres(DTO\Create\Input $input)
    {
        if ($input->genres) {
            $genresDb = $this->genreFactory->findByIds($input->genres);
            $genresDiff = array_diff($input->genres, $genresDb);
            if ($genresDiff) {
                throw new Exceptions\GenreNotFound('Genres not found', $genresDiff);
            }

            if ($input->categories) {
                $categoriesDb = $this->genreFactory->findByIdsWithCategories($input->genres, $input->categories);
                $categoriesDiff = array_diff($input->categories, $categoriesDb);

                if ($categoriesDiff) {
                    throw new Exceptions\CategoryGenreNotFound('Categories not found', $categoriesDiff);
                }
            }
        }
    }

    protected function verifyCastMembers(DTO\Create\Input $input)
    {
        if ($input->castMembers) {
            $castMembersDb = $this->castMemberFactory->findByIds($input->castMembers);
            $castMembersDiff = array_diff($input->castMembers, $castMembersDb);
            if ($castMembersDiff) {
                throw new Exceptions\GenreNotFound('Cast Members not found', $castMembersDiff);
            }
        }
    }

    protected function output(Video $entity)
    {
        return new DTO\Create\Output(
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
