<?php

namespace Tests\Unit\App\Models;

use App\Models\CastMember;
use Illuminate\Database\Eloquent\Model;
use Tests\Unit\TestCase;

class CastMemberTest extends TestCase
{
    private function model(): Model
    {
        return new CastMember();
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
            'type',
            'is_active',
        ];
        $model = $this->model();
        $this->assertEquals($fillableNeed, $model->fillable);
    }

    public function testCasts()
    {
        $fillableNeed = [
            'is_active' => 'boolean',
            'type' => 'int',
            'deleted_at' => 'datetime',
        ];
        $model = $this->model();
        $this->assertEquals($fillableNeed, $model->getCasts());
    }
}
