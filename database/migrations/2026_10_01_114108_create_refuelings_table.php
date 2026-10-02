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
        Schema::create('refuelings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('refueled_at');
            $table->unsignedInteger('mileage');
            $table->decimal('liters', 8, 3);
            $table->decimal('price_per_liter', 8, 3);
            $table->boolean('full_tank')->default(false);

            $table->timestamps();

            $table->index(['vehicle_id', 'refueled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refuelings');
    }
};
