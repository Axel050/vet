<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plan_prices', function (Blueprint $table) {
            $table->id();
            $table->string('plan')->unique();
            $table->integer('price');
            $table->timestamps();
        });

        // Insert default values
        DB::table('plan_prices')->insert([
            ['plan' => 'free', 'price' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['plan' => 'basic', 'price' => 500, 'created_at' => now(), 'updated_at' => now()],
            ['plan' => 'pro', 'price' => 1000, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_prices');
    }
};
