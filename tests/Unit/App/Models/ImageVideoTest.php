<?php

namespace Tests\Unit\App\Models;

use App\Models\ImageVideo;
use Illuminate\Database\Eloquent\Model;
use Tests\Unit\TestCase;

class ImageVideoTest extends TestCase
{
    private function model(): Model
    {
        return new ImageVideo();
    }

    public function testIfUseTraits()
    {
        $traitsNeeded = [
            \Illuminate\Database\Eloquent\Factories\HasFactory::class,
            \Illuminate\Database\Eloquent\Concerns\HasUuids::class,
            \Illuminate\Database\Eloquent\SoftDeletes::class,
        ];
        $model = $this->model();
        $this->assertEquals($traitsNeeded, array_values(class_uses($model)));
    }

    public function testFillable()
    {
        $fillableNeed = [
            'path',
            'type',
        ];
        $model = $this->model();
        $this->assertEquals($fillableNeed, $model->fillable);
    }

    public function testCasts()
    {
        $fillableNeed = [
            'deleted_at' => 'datetime'
        ];
        $model = $this->model();
        $this->assertEquals($fillableNeed, $model->getCasts());
    }
}
