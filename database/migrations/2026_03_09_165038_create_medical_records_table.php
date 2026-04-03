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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veterinary_id')->constrained('veterinaries')->cascadeOnDelete();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->foreignId('veterinary_type_id')->nullable()
                ->constrained()->nullOnDelete();

            $table->string('custom_type_name')->nullable();

            $table->decimal('price', 10, 2);
            $table->text('notes')->nullable();
            $table->text('notes_inside')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->date('performed_at');

            $table->decimal('temperature', 5, 2)->nullable();
            $table->integer('heart_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->text('anamnesis')->nullable();
            $table->text('physical_exam_details')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('prognosis')->nullable();
            $table->text('treatment_plan')->nullable();

            // Public / Owner Facing
            $table->text('prescriptions')->nullable();
            $table->text('recommendations')->nullable();
            $table->date('next_appointment_at')->nullable();
            $table->boolean('is_visible_to_owner')->default(true);

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
