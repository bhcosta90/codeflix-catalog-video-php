<?php

namespace Tests\Unit\App\Models;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Model;
use Tests\Unit\TestCase;

class GenreTest extends TestCase
{
    private function model(): Model
    {
        return new Genre();
    }

    public function testIfUseTraits()
    {
        $traitsNeeded = [
            \Illuminate\Database\Eloquent\Factories\HasFactory::class,
            \Illuminate\Database\Eloquent\SoftDeletes::class,
            \Illuminate\Database\Eloquent\Concerns\HasUuids::class,
        ];
        $model = $this->model();
        $this->assertEquals($traitsNeeded, array_values(class_uses($model)));
    }

    public function testFillable()
    {
        $fillableNeed = [
            'id',
            'name',
            'is_active'
        ];
        $model = $this->model();
        $this->assertEquals($fillableNeed, $model->fillable);
    }

    public function testCasts()
    {
        $fillableNeed = [
            'is_active' => 'boolean',
            'deleted_at' => 'datetime'
        ];
        $model = $this->model();
        $this->assertEquals($fillableNeed, $model->getCasts());
    }
}
