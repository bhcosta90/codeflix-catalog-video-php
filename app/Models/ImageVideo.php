<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageVideo extends Model
{
    use HasFactory, HasUuids;

    public $fillable = [
        'path',
        'type',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
