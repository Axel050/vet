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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veterinary_id')->constrained('veterinaries')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->foreignId('species_id')->nullable();
            $table->string('specie_custom')->nullable();
            $table->foreignId('breed_id')->nullable();
            $table->string('breed_custom')->nullable();

            $table->string('name');
            $table->year('birth_year')->nullable();
            $table->string('gender')->nullable(); // Male, Female, etc.

            $table->uuid('public_token')->unique();

            $table->date('date_of_birth')->nullable();
            $table->string('microchip_id')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_sterilized')->default(false);
            $table->text('allergies')->nullable();
            $table->text('chronic_medications')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('photo_path')->nullable();
            $table->string('blood_type')->nullable();

            $table->softDeletes();

            $table->unique(['veterinary_id', 'name', 'customer_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
