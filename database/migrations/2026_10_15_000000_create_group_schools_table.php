<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('director_name');
            $table->string('phone', 50);
            $table->string('address', 1000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_schools');
    }
};
