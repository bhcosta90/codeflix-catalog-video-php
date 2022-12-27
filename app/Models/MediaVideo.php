<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaVideo extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public $fillable = [
        'file_path',
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
