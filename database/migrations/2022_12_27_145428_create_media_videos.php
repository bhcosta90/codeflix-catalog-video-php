<?php

use App\Enums\MediaTypes;
use Core\Video\Domain\ValueObject\Enum\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_videos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('video_id')->index();
            $table->foreign('video_id')->references('id')->on('videos');
            $table->string('path');
            $table->string('encoded_path')->nullable();
            $table->enum('media_status', array_keys(Status::cases()))
                ->default(Status::COMPLETED->value);
            $table->enum('type', array_keys(MediaTypes::cases()));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_videos');
    }
};
