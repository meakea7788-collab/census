<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('census_teacher')) {
            Schema::create('census_teacher', function (Blueprint $table) {
                $table->id();
                $table->foreignId('census_id')->constrained('census')->cascadeOnDelete();
                $table->foreignId('teacher_id')->constrained('teacher')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['census_id', 'teacher_id']);
            });
        }

        if (Schema::hasColumn('census', 'teacher_id')) {
            $assignments = DB::table('census')
                ->whereNotNull('teacher_id')
                ->get(['id', 'teacher_id']);

            foreach ($assignments as $assignment) {
                DB::table('census_teacher')->updateOrInsert([
                    'census_id' => $assignment->id,
                    'teacher_id' => $assignment->teacher_id,
                ], [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::table('census', function (Blueprint $table) {
                $table->dropForeign(['teacher_id']);
                $table->dropColumn('teacher_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('census', 'teacher_id')) {
            Schema::table('census', function (Blueprint $table) {
                $table->foreignId('teacher_id')->nullable()->constrained('teacher')->nullOnDelete();
            });

            DB::table('census_teacher')->orderBy('census_id')->orderBy('id')->get()->each(function ($assignment) {
                DB::table('census')->where('id', $assignment->census_id)->update(['teacher_id' => $assignment->teacher_id]);
            });
        }

        Schema::dropIfExists('census_teacher');
    }
};
