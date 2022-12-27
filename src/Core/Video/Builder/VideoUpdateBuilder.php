<?php

namespace Core\Video\Builder;

use Core\Video\Domain\Entity\Video;
use Core\Video\Interfaces\VideoBuilderInterface;
use Core\Video\UseCase\DTO\Create\Input;
use Shared\ValueObject\Uuid;

class VideoUpdateBuilder extends VideoCreateBuilder implements VideoBuilderInterface
{
    protected Video $entity;

    public function createEntity(Input $input): self
    {
        $this->entity = new Video(
            id: new Uuid($input->id),
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
}
