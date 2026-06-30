<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estates', function (Blueprint $table) {
            $table->id();
            $table->string('name');                             // e.g. "Rongai", "Roysambu"
            $table->string('slug')->unique();                   // e.g. "rongai"
            $table->string('county')->default('Nairobi');
            $table->string('sub_county')->nullable();           // e.g. "Langata"
            $table->string('ward')->nullable();                 // e.g. "Kilimani Ward"
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('listing_count')->default(0); // denormalized for speed
            $table->timestamps();

            $table->index('county');
            $table->index('ward');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estates');
    }
};
