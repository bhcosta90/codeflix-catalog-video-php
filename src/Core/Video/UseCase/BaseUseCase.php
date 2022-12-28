<?php

namespace Core\Video\UseCase;

use Core\Video\Builder\VideoCreateBuilder;
use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\Repository\VideoRepositoryInterface;
use Core\Video\Factory\{CastMemberFactoryInterface, CategoryFactoryInterface, GenreFactoryInterface};
use Core\Video\Interfaces\VideoBuilderInterface;
use Costa\DomainPackage\UseCase\Interfaces\{DatabaseTransactionInterface, FileStorageInterface};
use Tests\Unit\Core\Video\Event\VideoEventManagerInterface;

abstract class BaseUseCase
{
    protected VideoBuilderInterface $builder;

    public function __construct(
        protected VideoRepositoryInterface $repository,
        protected DatabaseTransactionInterface $transaction,
        protected FileStorageInterface $storage,
        protected VideoEventManagerInterface $eventManager,
        protected CategoryFactoryInterface $categoryFactory,
        protected GenreFactoryInterface $genreFactory,
        protected CastMemberFactoryInterface $castMemberFactory,
    ) {
        $this->builder = $this->builder();
    }

    public abstract function builder(): VideoBuilderInterface;

    protected function storageAllFiles(object $input): array
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

    protected function verifyCategories(object $input)
    {
        if ($input->categories) {
            $categoriesDb = $this->categoryFactory->findByIds($input->categories);
            $categoriesDiff = array_diff($input->categories, $categoriesDb);
            if ($categoriesDiff) {
                throw new Exceptions\CategoryNotFound('Categories not found', $categoriesDiff);
            }

            if ($input->genres) {
                $genresDb = $this->categoryFactory->findByIdsWithGenres($input->genres, $input->categories);
                $genresDiff = array_diff($input->categories, $genresDb);

                if ($genresDiff) {
                    throw new Exceptions\CategoryGenreNotFound('Categories not found', $genresDiff);
                }
            }
        }
    }

    protected function verifyGenres(object $input)
    {
        if ($input->genres) {
            $genresDb = $this->genreFactory->findByIds($input->genres);
            $genresDiff = array_diff($input->genres, $genresDb);
            if ($genresDiff) {
                throw new Exceptions\GenreNotFound('Genres not found', $genresDiff);
            }
        }
    }

    protected function verifyCastMembers(object $input)
    {
        if ($input->castMembers) {
            $castMembersDb = $this->castMemberFactory->findByIds($input->castMembers);
            $castMembersDiff = array_diff($input->castMembers, $castMembersDb);
            if ($castMembersDiff) {
                throw new Exceptions\CastMemberNotFound('Cast Members not found', $castMembersDiff);
            }
        }
    }
}
