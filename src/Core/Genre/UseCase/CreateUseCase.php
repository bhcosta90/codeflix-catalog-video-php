<?php

namespace Core\Genre\UseCase;

use Core\Genre\Domain\Entity\GenreEntity;
use Core\Genre\Domain\Repository\GenreRepositoryInterface;
use Core\Genre\Factory\CategoryFactoryInterface;
use Shared\UseCase\Exception\UseCaseException;
use Shared\UseCase\Interfaces\DatabaseTransactionInterface;
use Throwable;

class CreateUseCase
{
    public function __construct(
        protected GenreRepositoryInterface $repository,
        protected CategoryFactoryInterface $categoryFactory,
        protected DatabaseTransactionInterface $transaction
    ) {
        //
    }

    public function execute(DTO\Create\Input $input): DTO\Create\Output
    {
        $entity = new GenreEntity(
            name: $input->name,
            categories: $input->categories,
        );

        if (!$input->is_active) {
            $entity->disabled();
        }

        $this->verifyCategories($input);

        try {
            if ($this->repository->insert($entity)) {
                $this->transaction->commit();
                return new DTO\Create\Output(
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

    private function verifyCategories(DTO\Create\Input $input)
    {
        if ($input->categories) {
            $categoriesDb = $this->categoryFactory->findByIds($input->categories);
            $categoriesDiff = array_diff($input->categories, $categoriesDb);
            if ($categoriesDiff) {
                throw new Exceptions\CategoryNotFound('Categories not found', $categoriesDiff);
            }
        }
    }
}
