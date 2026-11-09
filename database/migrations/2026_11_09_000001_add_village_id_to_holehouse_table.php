<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('holehouse', 'village_id')) {
            Schema::table('holehouse', function (Blueprint $table) {
                $table->foreignId('village_id')
                    ->nullable()
                    ->constrained('village')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('holehouse', 'village_id')) {
            Schema::table('holehouse', function (Blueprint $table) {
                $table->dropForeign(['village_id']);
                $table->dropColumn('village_id');
            });
        }
    }
};
