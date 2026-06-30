<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['photo', 'video']);
            $table->string('url');                     // Cloudinary / S3 URL
            $table->string('thumbnail_url')->nullable(); // For video thumbnails
            $table->string('public_id')->nullable();   // Cloudinary public_id for deletion
            $table->boolean('is_primary')->default(false); // Cover photo
            $table->unsignedTinyInteger('order')->default(0); // Display order
            $table->unsignedInteger('file_size')->nullable(); // bytes
            $table->string('mime_type')->nullable();   // image/jpeg, video/mp4
            $table->timestamps();

            $table->index(['listing_id', 'type']);
            $table->index(['listing_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_media');
    }
};
