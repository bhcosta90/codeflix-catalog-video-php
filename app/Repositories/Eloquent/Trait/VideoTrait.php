<?php

namespace App\Repositories\Eloquent\Trait;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use Core\Video\Domain\Entity\Video as Entity;
use Illuminate\Database\Eloquent\Model;

trait VideoTrait
{
    public function updateMediaVideo(Entity $entity, Model $model): void
    {
        if ($mediaVideo = $entity->videoFile) {
            $action = $model->media()->first() ? 'update' : 'create';
            $model->media()->{$action}([
                'path' => $mediaVideo->path,
                'media_status' => (string) $mediaVideo->status->value,
                'encoded_path' => $mediaVideo->encoded,
                'type' => (string) MediaTypes::VIDEO->value,
            ]);
        }
    }

    public function updateMediaTrailer(Entity $entity, Model $model): void
    {
        if ($trailer = $entity->trailerFile) {
            $action = $model->trailer()->first() ? 'update' : 'create';
            $model->trailer()->{$action}([
                'path' => $trailer->path,
                'media_status' => (string) $trailer->status->value,
                'encoded_path' => $trailer->encoded,
                'type' => (string) MediaTypes::TRAILER->value,
            ]);
        }
    }

    public function updateImageBanner(Entity $entity, Model $model): void
    {
        if ($banner = $entity->bannerFile) {
            $action = $model->banner()->first() ? 'update' : 'create';
            $model->banner()->{$action}([
                'path' => $banner->path,
                'type' => (string) ImageTypes::BANNER->value,
            ]);
        }
    }

    public function updateImageThumb(Entity $entity, Model $model): void
    {
        if ($thumb = $entity->thumbFile) {
            $action = $model->thumb()->first() ? 'update' : 'create';
            $model->thumb()->{$action}([
                'path' => $thumb->path,
                'type' => (string) ImageTypes::THUMB->value,
            ]);
        }
    }

    public function updateImageThumbHalf(Entity $entity, Model $model): void
    {
        if ($thumbHalf = $entity->thumbHalf) {
            $action = $model->thumbHalf()->first() ? 'update' : 'create';
            $model->thumbHalf()->{$action}([
                'path' => $thumbHalf->path,
                'type' => (string) ImageTypes::THUMB_HALF->value,
            ]);
        }
    }
}
