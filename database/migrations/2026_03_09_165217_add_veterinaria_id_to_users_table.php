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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('veterinary_id')
                ->nullable()
                ->after('id')
                ->constrained('veterinaries')
                ->nullOnDelete();

            $table->enum('role', [
                'super_admin',
                'owner',
                'admin',
                'staff',
            ])->default('owner')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
