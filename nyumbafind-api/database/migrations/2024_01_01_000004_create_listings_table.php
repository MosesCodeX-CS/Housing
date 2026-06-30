<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();

            // Ownership
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();   // landlord / caretaker
            $table->foreignId('estate_id')->constrained()->cascadeOnDelete();

            // Core listing info
            $table->string('title');                                           // e.g. "Spacious 1BR near stage"
            $table->text('description')->nullable();
            $table->enum('type', ['bedsitter', '1br', '2br', '3br', 'single_room', 'studio'])
                  ->default('bedsitter');
            $table->unsignedInteger('price');                                  // Monthly rent in KES
            $table->unsignedInteger('deposit')->nullable();                    // Usually 1–2 months rent
            $table->string('street')->nullable();                              // "Near Uchumi, off Ngong Rd"

            // Amenities — stored as JSON for flexibility
            $table->json('amenities')->nullable();
            // e.g. {"water": true, "electricity": true, "wifi": false,
            //        "parking": false, "security": true, "borehole": false}

            // Contact (caretaker details for WhatsApp)
            $table->string('caretaker_name')->nullable();
            $table->string('caretaker_phone', 15)->nullable();
            $table->string('caretaker_whatsapp', 15)->nullable();

            // Location
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Status & verification
            $table->enum('status', ['draft', 'pending', 'active', 'occupied', 'suspended'])
                  ->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();

            // Visibility
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_until')->nullable();

            // Stats
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('inquiry_count')->default(0);
            $table->timestamp('vacancy_confirmed_at')->nullable();             // Last time caretaker confirmed still vacant

            $table->timestamps();
            $table->softDeletes();

            // Indexes for common search queries
            $table->index(['estate_id', 'status']);
            $table->index(['type', 'price']);
            $table->index('status');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
