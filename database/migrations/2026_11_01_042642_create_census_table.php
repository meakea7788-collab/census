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
        Schema::create('census', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('year_id')->constrained('table_tbl_year')->onDelete('cascade');
            $table->foreignId('group_school_id')->constrained('group_schools')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('province_id')->constrained('province')->onDelete('cascade');
            $table->foreignId('district_id')->constrained('district')->onDelete('cascade');
            $table->foreignId('commune_id')->constrained('commune')->onDelete('cascade');
            $table->foreignId('village_id')->constrained('village')->onDelete('cascade');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('census');
    }
};
