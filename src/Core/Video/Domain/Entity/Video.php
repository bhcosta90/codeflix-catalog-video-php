<?php

namespace Core\Video\Domain\Entity;

use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;
use Shared\Domain\Entity\Trait\{EntityTrait, MethodsMagicsTrait, NotificationTrait};
use Shared\ValueObject\Uuid;
use DateTime;
use Shared\Domain\Notification\{DTO\Input};
use Shared\Domain\Notification\Exception\NotificationException;

class Video
{
    use MethodsMagicsTrait, EntityTrait, NotificationTrait;

    public function __construct(
        protected string $title,
        protected string $description,
        protected int $yearLaunched,
        protected int $duration,
        protected bool $opened,
        protected Rating $rating,
        protected array $categories = [],
        protected array $genres = [],
        protected ?Image $thumbFile = null,
        protected ?Image $thumbHalf = null,
        protected ?Image $bannerFile = null,
        protected ?Media $trailerFile = null,
        protected ?Media $videoFile = null,
        protected bool $publish = false,
        protected bool $isActive = true,
        protected ?Uuid $id = null,
        protected ?DateTime $createdAt = null,
    ) {
        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();
        $this->validate();
    }

    public function enabled(): void
    {
        $this->isActive = true;
    }

    public function disabled(): void
    {
        $this->isActive = false;
    }

    public function update(
        string $title,
        string $description,
        int $yearLaunched,
        int $duration,
        bool $opened,
        Rating $rating,
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->yearLaunched = $yearLaunched;
        $this->duration = $duration;
        $this->opened = $opened;
        $this->rating = $rating;
        $this->validate();
    }

    public function addCategory(string $category)
    {
        array_push($this->categories, $category);
    }

    public function subCategory(string $category)
    {
        $this->categories = array_diff($this->categories, [$category]);
    }

    public function addGenre(string $genre)
    {
        array_push($this->genres, $genre);
    }

    public function subGenre(string $genre)
    {
        $this->genres = array_diff($this->genres, [$genre]);
    }

    private function validate()
    {
        if (empty(trim($this->title))) {
            $this->getNotificationTrait()->addErrors(new Input(
                context: 'video',
                message: 'Title is required'
            ));
        }

        if (strlen(trim($this->title)) < 3) {
            $this->getNotificationTrait()->addErrors(new Input(
                context: 'video',
                message: 'The title must be at least 3 characters'
            ));
        }

        if (strlen(trim($this->title)) > 255) {
            $this->getNotificationTrait()->addErrors(new Input(
                context: 'video',
                message: 'The value must not be greater than 255 characters'
            ));
        }

        if ($this->getNotificationTrait()->hasErrors()) {
            throw new NotificationException(
                $this->getNotificationTrait()->message('video'),
                $this->getNotificationTrait()->getErrors()
            );
        }
    }
}
