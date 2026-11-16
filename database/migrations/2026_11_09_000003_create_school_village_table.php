<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_village', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('village_id')->constrained('village')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['school_id', 'village_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_village');
    }
};
