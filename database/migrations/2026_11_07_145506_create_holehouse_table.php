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
        Schema::create('holehouse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('census_id')->nullable()->constrained('census')->nullOnDelete();
            $table->integer('hole_number');
            $table->string('leader_family_name');
            $table->string('phone_number');
            $table->foreignId('village_id')->nullable()->constrained('village')->nullOnDelete();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holehouse');
    }
};
