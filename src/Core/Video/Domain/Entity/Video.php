<?php

namespace Core\Video\Domain\Entity;

use Core\Video\Domain\Enum\Rating;
use Core\Video\Domain\ValueObject\Image;
use Core\Video\Domain\ValueObject\Media;
use Core\Video\Domain\Factory\VideoValidator;
use Shared\ValueObject\Uuid;
use DateTime;
use Shared\Domain\Entity\Entity;
use Shared\Domain\Notification\{DTO\Input};
use Shared\Domain\Notification\Exception\NotificationException;

class Video extends Entity
{
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
        VideoValidator::create()->validate($this);

        if ($this->getNotification()->hasErrors()) {
            throw new NotificationException(
                $this->getNotification()->message('video'),
                $this->getNotification()->getErrors()
            );
        }
    }
}
