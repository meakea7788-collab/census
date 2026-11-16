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
        if (! Schema::hasTable('family')) {
            Schema::create('family', function (Blueprint $table) {
                $table->id();
                $table->foreignId('holehouse_id')->constrained('holehouse')->cascadeOnDelete();
                $table->string('family_name');
                $table->string('gender');
                $table->date('date_of_birth');
                $table->string('relationship');
                $table->string('job');
                $table->timestamps();
            });

            return;
        }

        if (! Schema::hasColumn('family', 'holehouse_id')) {
            Schema::table('family', function (Blueprint $table) {
                $table->foreignId('holehouse_id')
                    ->nullable()
                    ->constrained('holehouse')
                    ->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family');
    }
};
