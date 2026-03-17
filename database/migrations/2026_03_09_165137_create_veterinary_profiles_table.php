<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('veterinary_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veterinary_id')->constrained('veterinaries')->cascadeOnDelete();

            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->text('description')->nullable();

            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();

            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veterinary_profiles');
    }
};
