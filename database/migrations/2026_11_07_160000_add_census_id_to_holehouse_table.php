<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('holehouse', 'census_id')) {
            Schema::table('holehouse', function (Blueprint $table) {
                $table->foreignId('census_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('census')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('holehouse', 'census_id')) {
            Schema::table('holehouse', function (Blueprint $table) {
                $table->dropForeign(['census_id']);
                $table->dropColumn('census_id');
            });
        }
    }
};
