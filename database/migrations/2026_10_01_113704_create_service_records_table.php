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
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('service_date');
            $table->unsignedInteger('mileage');
            $table->string('type', 100);
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->date('next_service_date')->nullable();

            $table->timestamps();

            $table->index(['vehicle_id', 'service_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
