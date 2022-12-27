<?php

namespace Core\Video\Builder;

use Core\Video\Domain\Entity\Video;
use Core\Video\Domain\ValueObject\Enum\Status;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;
use Core\Video\Interfaces\VideoBuilderInterface;
use Core\Video\UseCase\DTO\Create\Input;
use Costa\DomainPackage\Domain\Entity\Entity;

class VideoCreateBuilder implements VideoBuilderInterface
{
    protected Video $entity;

    public function createEntity(Input $input): self
    {
        $this->entity = new Video(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating,
            categories: $input->categories,
            genres: $input->genres,
            castMembers: $input->castMembers,
        );

        return $this;
    }

    public function addVideo(string $path, Status $status = Status::PENDING): self
    {
        $media = new Media(
            path: $path,
            status: $status,
        );
        $this->entity->setVideoFile($media);
        return $this;
    }

    public function addTrailer(string $path, Status $status = Status::PENDING): self
    {
        $media = new Media(
            path: $path,
            status: $status,
        );
        $this->entity->setTrailerFile($media);
        return $this;
    }

    public function addThumb(string $path): self
    {
        $media = new Image(
            path: $path,
        );
        $this->entity->setThumbFile($media);
        return $this;
    }

    public function addThumbHalf(string $path): self
    {
        $media = new Image(
            path: $path,
        );
        $this->entity->setThumbHalf($media);
        return $this;
    }

    public function addBanner(string $path): self
    {
        $media = new Image(
            path: $path,
        );
        $this->entity->setBannerFile($media);
        return $this;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }
}
