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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('personal_info')->nullable();
            $table->json('medical_history')->nullable();
            $table->json('medications')->nullable();
            $table->json('allergies')->nullable();
            $table->json('prior_treatments')->nullable();
            $table->json('health_conditions')->nullable();
            $table->json('goals')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
