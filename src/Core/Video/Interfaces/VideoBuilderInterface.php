<?php

namespace Core\Video\Interfaces;

use Core\Video\Domain\ValueObject\Enum\Status;
use Costa\DomainPackage\Domain\Entity\Entity;

interface VideoBuilderInterface
{
    public function createEntity(object $input): self;

    public function addVideo(string $path, Status $status = Status::PENDING): self;

    public function addTrailer(string $path, Status $status = Status::PENDING): self;

    public function addThumb(string $path): self;

    public function addThumbHalf(string $path): self;

    public function addBanner(string $path): self;

    public function getEntity(): Entity;
}
