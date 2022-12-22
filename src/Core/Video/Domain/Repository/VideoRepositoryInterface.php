<?php

namespace Core\Video\Domain\Repository;

use Core\Video\Domain\Entity\Video;
use Shared\Domain\Repository\EntityRepositoryInterface;

interface VideoRepositoryInterface extends EntityRepositoryInterface
{
    public function updateMedia(Video $video): bool;
}
