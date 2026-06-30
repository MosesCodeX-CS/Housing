<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->text('message')->nullable();        // Optional message before WhatsApp
            $table->enum('status', ['new', 'whatsapp_opened', 'responded', 'closed'])
                  ->default('new');
            $table->timestamp('whatsapp_opened_at')->nullable(); // Clicked "Chat on WhatsApp"
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['listing_id', 'status']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
