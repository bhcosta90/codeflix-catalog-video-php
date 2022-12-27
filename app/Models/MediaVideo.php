<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaVideo extends Model
{
    use HasFactory, HasUuids;

    public $fillable = [
        'path',
        'encoded_path',
        'media_status',
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
