<?php

namespace Tests\Unit\App\Models;

use App\Models\MediaVideo;
use Illuminate\Database\Eloquent\Model;
use Tests\Unit\TestCase;

class MediaVideoTest extends TestCase
{
    private function model(): Model
    {
        return new MediaVideo();
    }

    public function testIfUseTraits()
    {
        $traitsNeeded = [
            \Illuminate\Database\Eloquent\Factories\HasFactory::class,
            \Illuminate\Database\Eloquent\Concerns\HasUuids::class,
        ];
        $model = $this->model();
        $this->assertEquals($traitsNeeded, array_values(class_uses($model)));
    }

    public function testFillable()
    {
        $fillableNeed = [
            'path',
            'encoded_path',
            'media_status',
            'type',
        ];
        $model = $this->model();
        $this->assertEquals($fillableNeed, $model->fillable);
    }

    public function testCasts()
    {
        $fillableNeed = [
            'deleted_at' => 'datetime',
        ];
        $model = $this->model();
        $this->assertEquals($fillableNeed, $model->getCasts());
    }
}
