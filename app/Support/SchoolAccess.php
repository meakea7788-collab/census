<?php

namespace App\Support;

use App\Models\Census;
use App\Models\family as Family;
use App\Models\hole_house;
use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SchoolAccess
{
    /**
     * Get the school ID for the current authenticated user.
     * Returns null for superadmin.
     */
    public static function schoolId(): ?int
    {
        $user = auth()->user();

        if (! $user || $user->role === User::ROLE_SUPERADMIN) {
            return null;
        }

        $schoolId = $user->school_id ?? $user->teacher?->school_id ?? $user->teachers()->value('teacher.school_id');
        abort_if(! $schoolId, 403, __('Your account is not linked to a school.'));

        return (int) $schoolId;
    }

    /**
     * Get the teacher IDs assigned to the given or current user.
     */
    public static function userTeacherIds(?User $user = null): array
    {
        $user ??= auth()->user();
        if (! $user) {
            return [];
        }

        $teacherIds = $user->teachers()->pluck('teacher.id')->all();
        if (empty($teacherIds) && $user->teacher_id) {
            $teacherIds = [(int) $user->teacher_id];
        }

        return array_map('intval', array_unique($teacherIds));
    }

    /**
     * Get the census IDs assigned to the given or current user (for ROLE_USER).
     * Returns null if not restricted to specific user censuses (e.g. superadmin, admin, or user without teacher assignment).
     * Returns an array of integers (which may be empty if teachers have no censuses assigned yet).
     */
    public static function userCensusIds(?User $user = null): ?array
    {
        $user ??= auth()->user();
        if (! $user || $user->role !== User::ROLE_USER) {
            return null;
        }

        $teacherIds = self::userTeacherIds($user);
        if (empty($teacherIds)) {
            return null; // Fallback to school
        }

        $schoolId = $user->school_id ?? $user->teacher?->school_id;

        return DB::table('census_teacher')
            ->join('census', 'census_teacher.census_id', '=', 'census.id')
            ->whereIn('census_teacher.teacher_id', $teacherIds)
            ->when($schoolId !== null, fn ($q) => $q->where('census.school_id', $schoolId))
            ->pluck('census_teacher.census_id')
            ->unique()
            ->values()
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public static function userVillageIds(?User $user = null): ?array
    {
        $censusIds = self::userCensusIds($user);
        if ($censusIds === null) {
            return null;
        }
        if (empty($censusIds)) {
            return [];
        }

        return DB::table('census')
            ->whereIn('id', $censusIds)
            ->whereNotNull('village_id')
            ->pluck('village_id')
            ->unique()
            ->values()
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Restrict census query:
     * - SuperAdmin: all censuses
     * - Admin: censuses of their school ("data follow school")
     * - Normal User: censuses of their assigned teachers ("data follow user"), or school fallback
     */
    public static function censusQuery(): Builder
    {
        $user = auth()->user();
        $query = Census::query();

        if (! $user || $user->role === User::ROLE_SUPERADMIN) {
            return $query;
        }

        if ($user->role === User::ROLE_USER) {
            $censusIds = self::userCensusIds($user);
            if ($censusIds !== null) {
                return $query->whereIn('census.id', $censusIds);
            }
        }

        $schoolId = self::schoolId();
        return $schoolId !== null ? $query->where('census.school_id', $schoolId) : $query;
    }

    /**
     * Restrict hole house query:
     * - SuperAdmin: all hole houses
     * - Admin: hole houses belonging to censuses of their school ("data follow school")
     * - Normal User: hole houses belonging to user's assigned censuses or villages ("data follow user")
     */
    public static function holeHousesQuery(): Builder
    {
        $user = auth()->user();
        $query = hole_house::query();

        if (! $user || $user->role === User::ROLE_SUPERADMIN) {
            return $query;
        }

        if ($user->role === User::ROLE_USER) {
            $censusIds = self::userCensusIds($user);
            if ($censusIds !== null) {
                $villageIds = self::userVillageIds($user) ?? [];
                $schoolId = $user->school_id ?? $user->teacher?->school_id ?? $user->teachers()->value('teacher.school_id');

                return $query->where(function ($q) use ($censusIds, $villageIds, $schoolId) {
                    $q->whereIn('holehouse.census_id', $censusIds);
                    if (! empty($villageIds)) {
                        $q->orWhere(function ($vq) use ($villageIds, $schoolId) {
                            $vq->whereIn('holehouse.village_id', $villageIds)
                                ->when($schoolId !== null, fn ($sq) => $sq->whereHas('census', fn ($cq) => $cq->where('school_id', $schoolId)));
                        });
                    }
                });
            }
        }

        $schoolId = self::schoolId();
        return $query->whereHas('census', fn ($q) => $q->when($schoolId !== null, fn ($q) => $q->where('school_id', $schoolId)));
    }

    /**
     * Restrict family query:
     * - SuperAdmin: all families
     * - Admin: families in hole houses belonging to censuses of their school ("data follow school")
     * - Normal User: families in hole houses belonging to user's assigned censuses or villages ("data follow user")
     */
    public static function familiesQuery(): Builder
    {
        $user = auth()->user();
        $query = Family::query();

        if (! $user || $user->role === User::ROLE_SUPERADMIN) {
            return $query;
        }

        $houseIdsSubquery = self::holeHousesQuery()->select('holehouse.id');

        return $query->whereIn('family.holehouse_id', $houseIdsSubquery);
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

    public static function accessibleVillages()
    {
        $user = auth()->user();

        $villageIds = self::holeHousesQuery()
            ->whereNotNull('village_id')
            ->distinct()
            ->pluck('village_id')
            ->merge(
                self::censusQuery()
                    ->whereNotNull('village_id')
                    ->distinct()
                    ->pluck('village_id')
            );

        if ($user && ($user->role === User::ROLE_ADMIN || ($user->role === User::ROLE_USER && self::userCensusIds($user) === null))) {
            $schoolId = self::schoolId();
            if ($schoolId !== null) {
                $schoolVillageIds = DB::table('school_village')
                    ->where('school_id', $schoolId)
                    ->pluck('village_id');
                $villageIds = $villageIds->merge($schoolVillageIds);
            }
        }

        $villageIds = $villageIds->filter()->unique()->values()->all();

        return DB::table('village')
            ->select('id', 'name')
            ->when(
                $user?->role !== User::ROLE_SUPERADMIN || ! empty($villageIds),
                fn ($query) => $query->whereIn('id', $villageIds)
            )
            ->orderBy('name')
            ->get();
    }

    /**
     * Carry over census records (and keep all hole house & family data)
     * from previous years into the target year.
     */
    public static function carryOverCensusDataToYear(int $targetYearId, ?int $schoolId = null): int
    {
        $previousCensuses = DB::table('census')
            ->join('year', 'census.year_id', '=', 'year.id')
            ->where('census.year_id', '!=', $targetYearId)
            ->when($schoolId !== null, fn ($q) => $q->where('census.school_id', $schoolId))
            ->select('census.*', 'year.year as year_number')
            ->orderByDesc('year.year')
            ->orderByDesc('census.id')
            ->get();

        $latestPerVillage = $previousCensuses->unique(fn ($c) => $c->school_id . ':' . $c->village_id);

        $count = 0;

        DB::transaction(function () use ($latestPerVillage, $targetYearId, &$count) {
            $oldToNewCensusIds = [];

            foreach ($latestPerVillage as $oldCensus) {
                $existingTargetCensus = DB::table('census')
                    ->where('year_id', $targetYearId)
                    ->where('school_id', $oldCensus->school_id)
                    ->where('village_id', $oldCensus->village_id)
                    ->first();

                if ($existingTargetCensus) {
                    $newCensusId = $existingTargetCensus->id;
                } else {
                    $newCensusId = DB::table('census')->insertGetId([
                        'year_id' => $targetYearId,
                        'group_school_id' => $oldCensus->group_school_id,
                        'school_id' => $oldCensus->school_id,
                        'province_id' => $oldCensus->province_id,
                        'district_id' => $oldCensus->district_id,
                        'commune_id' => $oldCensus->commune_id,
                        'village_id' => $oldCensus->village_id,
                        'note' => $oldCensus->note,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $teacherIds = DB::table('census_teacher')
                        ->where('census_id', $oldCensus->id)
                        ->pluck('teacher_id')
                        ->unique()
                        ->all();

                    if (! empty($teacherIds)) {
                        DB::table('census_teacher')->insert(array_map(fn ($teacherId) => [
                            'census_id' => $newCensusId,
                            'teacher_id' => $teacherId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ], $teacherIds));
                    }

                    $count++;
                }

                $oldToNewCensusIds[$oldCensus->id] = $newCensusId;

                $allSchoolCensusIds = DB::table('census')
                    ->where('school_id', $oldCensus->school_id)
                    ->pluck('id')
                    ->all();

                if (! empty($allSchoolCensusIds)) {
                    DB::table('holehouse')
                        ->whereIn('census_id', $allSchoolCensusIds)
                        ->where('village_id', $oldCensus->village_id)
                        ->update([
                            'census_id' => $newCensusId,
                            'updated_at' => now(),
                        ]);
                }
            }

            // Also carry over any remaining houses attached to old censuses (e.g. sibling villages in same commune)
            foreach ($oldToNewCensusIds as $oldId => $newId) {
                DB::table('holehouse')
                    ->where('census_id', $oldId)
                    ->update([
                        'census_id' => $newId,
                        'updated_at' => now(),
                    ]);
            }
        });

        return $count;
    }
}
