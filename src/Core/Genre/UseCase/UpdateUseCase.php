<?php

namespace Core\Genre\UseCase;

use Core\Genre\Domain\Entity\GenreEntity;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Genre\Factory\CategoryFactoryInterface;
use Shared\UseCase\Exception\NotFoundException;
use Shared\UseCase\Exception\UseCaseException;
use Shared\UseCase\Interfaces\DatabaseTransactionInterface;
use Throwable;

class UpdateUseCase
{
    public function __construct(
        protected GenreRepositoryInterface $repository,
        protected CategoryFactoryInterface $categoryFactory,
        protected DatabaseTransactionInterface $transaction
    ) {
        //
    }

    public function execute(DTO\Update\Input $input): DTO\Update\Output
    {
        if ($entity = $this->repository->findById($input->id)) {
            $entity->update(
                name: $input->name,
            );
            $input->is_active ? $entity->enabled() : $entity->disabled();
            $this->verifyCategories($input);

            foreach ($input->categories as $category) {
                $entity->addCategory($category);
            }

            try {
                if ($this->repository->update($entity)) {
                    $this->transaction->commit();
                    return new DTO\Update\Output(
                        id: $entity->id(),
                        name: $entity->name,
                        categories: $entity->categories,
                        is_active: $entity->isActive,
                        created_at: $entity->createdAt(),
                    );
                }
            } catch (Throwable $e) {
                $this->transaction->rollback();
                throw $e;
            }

            throw new UseCaseException(self::class);
        }

        throw new NotFoundException($input->id);
    }

    private function verifyCategories(DTO\Update\Input $input)
    {
        $categoriesDb = $this->categoryFactory->findByIds($input->categories);
        $categoriesDiff = array_diff($input->categories, $categoriesDb);

        if ($categoriesDiff) {
            throw new Exceptions\CategoryNotFound('Categories not found', $categoriesDiff);
        }
    }
}
