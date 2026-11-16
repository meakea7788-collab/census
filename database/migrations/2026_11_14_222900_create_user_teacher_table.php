<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teacher')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'teacher_id']);
        });

        if (Schema::hasColumn('users', 'teacher_id')) {
            DB::table('users')
                ->whereNotNull('teacher_id')
                ->orderBy('id')
                ->get(['id', 'teacher_id'])
                ->each(function ($user): void {
                    DB::table('user_teacher')->insert([
                        'user_id' => $user->id,
                        'teacher_id' => $user->teacher_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_teacher');
    }
};
