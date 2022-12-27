<?php

namespace Core\Video\UseCase;

use Core\Video\Builder\VideoCreateBuilder;
use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Factory\{CastMemberFactoryInterface, CategoryFactoryInterface, GenreFactoryInterface};
use Costa\DomainPackage\UseCase\Interfaces\{DatabaseTransactionInterface, FileStorageInterface};
use Tests\Unit\Core\Video\Event\VideoEventManagerInterface;

class BaseUseCase
{
    protected VideoCreateBuilder $builder;

    public function __construct(
        protected VideoRepositoryInterface $repository,
        protected DatabaseTransactionInterface $transaction,
        protected FileStorageInterface $storage,
        protected VideoEventManagerInterface $eventManager,
        protected CategoryFactoryInterface $categoryFactory,
        protected GenreFactoryInterface $genreFactory,
        protected CastMemberFactoryInterface $castMemberFactory,
    ) {
        $this->builder = new VideoCreateBuilder();
    }

    protected function createEntity(DTO\Create\Input $input): Video
    {
        $entity = new Video([
            'title' => $input->title,
            'description' => $input->description,
            'yearLaunched' => $input->yearLaunched,
            'duration' => $input->duration,
            'opened' => true,
            'rating' => $input->rating,
            'categories' => $input->categories,
            'genres' => $input->genres,
            'castMembers' => $input->castMembers,
        ]);

        $this->verifyCategories($input);
        $this->verifyGenres($input);
        $this->verifyCastMembers($input);

        return $entity;
    }

    protected function storageAllFiles(DTO\Create\Input $input): array
    {
        $result = [];
        $pathVideo = $this->builder->getEntity()->id();

        if ($path = $this->storeMedia($pathVideo, $input->videoFile)) {
            array_push($result, $path);
            $this->builder->addVideo($path);
        }

        if ($path = $this->storeMedia($pathVideo, $input->trailerFile)) {
            array_push($result, $path);
            $this->builder->addTrailer($path);
        }

        if ($path = $this->storeMedia($pathVideo, $input->bannerFile)) {
            array_push($result, $path);
            $this->builder->addBanner($path);
        }

        if ($path = $this->storeMedia($pathVideo, $input->thumbFile)) {
            array_push($result, $path);
            $this->builder->addThumb($path);
        }

        if ($path = $this->storeMedia($pathVideo, $input->thumbHalf)) {
            array_push($result, $path);
            $this->builder->addThumbHalf($path);
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
}
