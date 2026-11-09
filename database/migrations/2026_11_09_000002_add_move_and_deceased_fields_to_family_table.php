<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family', function (Blueprint $table) {
            if (! Schema::hasColumn('family', 'moved_to_address')) {
                $table->text('moved_to_address')->nullable();
            }

            if (! Schema::hasColumn('family', 'is_deceased')) {
                $table->boolean('is_deceased')->default(false);
            }
        });
    }

    public function down(): void
    {
        $columns = [];

        if (Schema::hasColumn('family', 'moved_to_address')) {
            $columns[] = 'moved_to_address';
        }

        if (Schema::hasColumn('family', 'is_deceased')) {
            $columns[] = 'is_deceased';
        }

        if ($columns !== []) {
            Schema::table('family', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
