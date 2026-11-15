<?php

namespace App\Support;

use App\Models\Census;
use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SchoolAccess
{
    public static function schoolId(): ?int
    {
        $user = auth()->user();

        if ($user?->role !== User::ROLE_ADMIN) {
            return null;
        }

        $schoolId = $user->school_id ?? $user->teacher?->school_id;
        abort_if(! $schoolId, 403, __('Your admin account is not linked to a school.'));

        return (int) $schoolId;
    }

    public static function restrict(Builder $query, string $column = 'school_id'): Builder
    {
        $schoolId = self::schoolId();

        return $schoolId === null ? $query : $query->where($column, $schoolId);
    }

    public static function authorizeSchool(int|string $schoolId): void
    {
        $allowedSchoolId = self::schoolId();
        abort_if($allowedSchoolId !== null && (int) $schoolId !== $allowedSchoolId, 403);
    }

    public static function authorizeTeacher(Teacher $teacher): void
    {
        self::authorizeSchool($teacher->school_id);
    }

    public static function authorizeCensus(Census $census): void
    {
        self::authorizeSchool($census->school_id);
    }

    public static function schoolQuery(): Builder
    {
        return self::restrict(School::query(), 'schools.id');
    }

    public static function teacherQuery(): Builder
    {
        return self::restrict(Teacher::query(), 'teacher.school_id');
    }

    public static function userAssignmentTeacherQuery(): Builder
    {
        $schoolId = self::schoolId();

        return self::teacherQuery()
            ->whereHas('censuses', fn ($censuses) => $censuses
                ->when($schoolId !== null, fn ($censuses) => $censuses->where('census.school_id', $schoolId)))
            ->with([
                'school',
                'censuses' => fn ($query) => $query
                    ->when($schoolId !== null, fn ($query) => $query->where('census.school_id', $schoolId))
                    ->with(['groupSchool', 'village']),
            ]);
    }

    public static function canAssignTeacherToUser(int $teacherId): bool
    {
        $schoolId = self::schoolId();

        return Teacher::query()
            ->when($schoolId !== null, fn ($query) => $query->where('teacher.school_id', $schoolId))
            ->whereKey($teacherId)
            ->whereHas('censuses', fn ($query) => $query
                ->when($schoolId !== null, fn ($query) => $query->where('census.school_id', $schoolId)))
            ->exists();
    }

    public static function validateTeacherAssignments(array $teacherIds, bool $mustShareSchool = false): void
    {
        foreach ($teacherIds as $teacherId) {
            abort_unless(self::canAssignTeacherToUser((int) $teacherId), 403);
        }

        if ($mustShareSchool && count($teacherIds) > 1) {
            $schoolIds = Teacher::whereIn('id', $teacherIds)->distinct()->pluck('school_id');
            abort_unless($schoolIds->count() === 1, 422, __('Admin teachers must belong to the same school.'));
        }
    }
}
