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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('brand', 150);
            $table->string('model', 150);
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('mileage');
            $table->string('registration_number', 50);
            $table->string('fuel_type');
            $table->timestamps();

            $table->unique([
                'user_id',
                'registration_number',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
