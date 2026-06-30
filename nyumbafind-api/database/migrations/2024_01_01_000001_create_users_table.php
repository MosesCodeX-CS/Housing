<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 15)->unique();              // +2547XXXXXXXX
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();             // nullable: OTP-only users
            $table->enum('role', ['tenant', 'landlord', 'caretaker', 'agent', 'admin'])
                  ->default('tenant');
            $table->string('avatar')->nullable();
            $table->string('whatsapp_number', 15)->nullable();  // may differ from phone
            $table->boolean('is_active')->default(true);
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
