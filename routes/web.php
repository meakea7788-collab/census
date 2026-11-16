<?php

use App\Http\Controllers\GroupSchoolController;
use App\Models\User;
use App\Support\SchoolAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome-page');
})->name('welcome');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $credentials = $request->only('email', 'password');
    $credentials['is_active'] = true;

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('status', __('Login successful!'));
    }

    return back()->withErrors([
        'email' => __('The provided credentials do not match our records.'),
    ])->onlyInput('email');
})->name('login.submit');

Route::post('/logout', function (Request $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('welcome');
})->name('logout');

Route::post('/language', function (Request $request) {
    $data = $request->validate([
        'locale' => ['required', 'in:en,km'],
    ]);

    $request->session()->put('locale', $data['locale']);

    return redirect()->back();
})->name('language.switch');

// Routes accessible to all authenticated users (Normal User, Admin, Superadmin)
Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        $holeHousesCount = SchoolAccess::holeHousesQuery()->count();
        $familiesCount = SchoolAccess::familiesQuery()->count();

        $villages = SchoolAccess::accessibleVillages();
        $holeHouses = SchoolAccess::holeHousesQuery()
            ->with(['families', 'census'])
            ->get();

        $villageStats = $villages->map(function ($village) use ($holeHouses) {
            $villageHouses = $holeHouses->filter(
                fn ($house) => (int) ($house->village_id ?? $house->census?->village_id) === (int) $village->id
            );
            $families = $villageHouses->flatMap->families;

            $totalMembers = $families->count();
            $femaleMembers = $families->filter(
                fn ($member) => in_array(mb_strtolower(trim((string) $member->gender)), ['ស្រី', 'female', 'f'], true)
            )->count();
            $maleMembers = $families->filter(
                fn ($member) => in_array(mb_strtolower(trim((string) $member->gender)), ['ប្រុស', 'male', 'm'], true)
            )->count();
            $occupiedHouses = $villageHouses->filter(
                fn ($house) => in_array($house->status, ['Occupied', 'មានអ្នកស្នាក់នៅ'], true)
            )->count();
            $vacantHouses = $villageHouses->filter(
                fn ($house) => in_array($house->status, ['Vacant', 'ទំនេរ'], true)
            )->count();
            $movedMembers = $families->filter(
                fn ($member) => ! empty($member->moved_to_address) && ! $member->is_deceased
            )->count();
            $deceasedMembers = $families->filter(
                fn ($member) => (bool) $member->is_deceased
            )->count();

            return (object) [
                'id' => $village->id,
                'name' => $village->name,
                'houses_count' => $villageHouses->count(),
                'occupied_houses' => $occupiedHouses,
                'vacant_houses' => $vacantHouses,
                'total_members' => $totalMembers,
                'female_members' => $femaleMembers,
                'male_members' => $maleMembers,
                'moved_members' => $movedMembers,
                'deceased_members' => $deceasedMembers,
            ];
        });

        return view('dashboard', compact('holeHousesCount', 'familiesCount', 'villageStats'));
    })->name('home');

    Route::get('/profile', function () {
        $user = User::with([
            'school',
            'teacher.school.groupSchool',
            'teacher.censuses.village',
            'teachers.school.groupSchool',
            'teachers.censuses.village',
        ])->findOrFail(Auth::id());

        return view('users.profile', compact('user'));
    })->name('profile');

    // Hole House routes
    Route::get('/hole_houses', [\App\Http\Controllers\hole_houseController::class, 'index'])->name('hole_houses.index');
    Route::get('/hole_houses/create', [\App\Http\Controllers\hole_houseController::class, 'create'])->name('hole_houses.create');
    Route::post('/hole_houses', [\App\Http\Controllers\hole_houseController::class, 'store'])->name('hole_houses.store');
    Route::get('/hole_houses/{id}/edit', [\App\Http\Controllers\hole_houseController::class, 'edit'])->name('hole_houses.edit');
    Route::put('/hole_houses/{id}', [\App\Http\Controllers\hole_houseController::class, 'update'])->name('hole_houses.update');
    Route::delete('/hole_houses/{id}', [\App\Http\Controllers\hole_houseController::class, 'destroy'])->name('hole_houses.destroy');
    Route::get('/hole_houses/{id}', [\App\Http\Controllers\hole_houseController::class, 'show'])->name('hole_houses.show');

    // Family routes
    Route::get('/family', [\App\Http\Controllers\FamilyController::class, 'index'])->name('family.index');
    Route::get('/family/create', [\App\Http\Controllers\FamilyController::class, 'create'])->name('family.create');
    Route::post('/family', [\App\Http\Controllers\FamilyController::class, 'store'])->name('family.store');
    Route::get('/family/{family}', [\App\Http\Controllers\FamilyController::class, 'show'])->name('family.show');
    Route::get('/family/{family}/edit', [\App\Http\Controllers\FamilyController::class, 'edit'])->name('family.edit');
    Route::put('/family/{family}', [\App\Http\Controllers\FamilyController::class, 'update'])->name('family.update');
    Route::delete('/family/{family}', [\App\Http\Controllers\FamilyController::class, 'destroy'])->name('family.destroy');
    Route::get('/family/{family}/move', [\App\Http\Controllers\FamilyController::class, 'editMoveAddress'])->name('family.move.edit');
    Route::put('/family/{family}/move', [\App\Http\Controllers\FamilyController::class, 'updateMoveAddress'])->name('family.move.update');
    Route::post('/family/{family}/mark-deceased', [\App\Http\Controllers\FamilyController::class, 'markDeceased'])->name('family.mark-deceased');
});

// Administrative routes (SuperAdmin and Admin only)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/users', function () {
        $schoolId = SchoolAccess::schoolId();
        $users = User::with(['school', 'teacher.school.groupSchool', 'teachers.school.groupSchool'])
            ->when($schoolId !== null, fn ($query) => $query->where(function ($query) use ($schoolId) {
                $query->where('users.school_id', $schoolId)
                    ->orWhereHas('teacher', fn ($teacher) => $teacher->where('school_id', $schoolId))
                    ->orWhereHas('teachers', fn ($teacher) => $teacher->where('teacher.school_id', $schoolId));
            }))
            ->orderBy('name')->get();

        return view('users.index', compact('users'));
    })->name('users.index');

    Route::get('/users/create', function () {
        $currentUser = Auth::user();
        $canAssignRoles = $currentUser->role === User::ROLE_SUPERADMIN;
        $schools = $canAssignRoles ? \App\Models\School::orderBy('name')->get() : collect();
        $teachers = $canAssignRoles ? collect() : SchoolAccess::userAssignmentTeacherQuery()->orderBy('name')->get();
        $requiresTeacher = $currentUser->role === User::ROLE_ADMIN;

        return view('users.create', compact('teachers', 'schools', 'canAssignRoles', 'requiresTeacher'));
    })->name('users.create');

    Route::post('/users/store', function (Request $request) {
        $currentUser = Auth::user();
        $teacherRequired = $currentUser->role === User::ROLE_ADMIN;
        if (! $request->exists('teacher_ids') && $request->filled('teacher_id')) {
            $request->merge(['teacher_ids' => [$request->input('teacher_id')]]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:6', 'max:255'],
            'role' => $currentUser->role === User::ROLE_SUPERADMIN
                ? ['nullable', 'string', 'in:' . implode(',', User::roles())]
                : ['prohibited'],
            'school_id' => $currentUser->role === User::ROLE_SUPERADMIN
                ? ['required', 'integer', 'exists:schools,id']
                : ['prohibited'],
            'teacher_ids' => $teacherRequired ? ['required', 'array', 'min:1'] : ['nullable', 'array'],
            'teacher_ids.*' => ['required', 'integer', 'distinct', 'exists:teacher,id'],
        ]);

        $teacherIds = $data['teacher_ids'] ?? [];
        unset($data['teacher_ids']);
        $targetRole = $currentUser->role === User::ROLE_SUPERADMIN
            ? ($data['role'] ?? User::ROLE_USER)
            : User::ROLE_USER;
        if ($teacherIds !== [] && $currentUser->role !== User::ROLE_SUPERADMIN) {
            SchoolAccess::validateTeacherAssignments($teacherIds, $targetRole === User::ROLE_ADMIN);
        }

        if ($currentUser->role === User::ROLE_SUPERADMIN) {
            $data['teacher_id'] = null;
        } else {
            $data['school_id'] = SchoolAccess::schoolId();
            $data['teacher_id'] = $teacherIds[0] ?? null;
        }
        $data['email'] = $data['email'] ?? strtolower(Str::slug($data['name'], '') ?: 'user') . uniqid() . '@example.com';
        $data['password'] = $data['password'] ?? 'password123';
        $data['role'] = $targetRole;

        $user = User::create($data);
        if ($currentUser->role !== User::ROLE_SUPERADMIN) {
            $user->teachers()->sync($teacherIds);
        }

        return redirect()->route('users.create')->with('status', __('User created successfully.'));
    })->name('users.store');

    Route::get('/users/{user}/edit', function (User $user) {
        $currentUser = Auth::user();
        abort_if($currentUser->role === User::ROLE_ADMIN && $user->role !== User::ROLE_USER, 403);
        $schoolId = SchoolAccess::schoolId();

        if ($schoolId !== null) {
            abort_unless(
                (int) $user->school_id === $schoolId
                    || $user->teacher?->school_id === $schoolId
                    || $user->teachers()->where('teacher.school_id', $schoolId)->exists(),
                404
            );
        }

        $canAssignRoles = $currentUser->role === User::ROLE_SUPERADMIN;
        $schools = $canAssignRoles ? \App\Models\School::orderBy('name')->get() : collect();
        $teachers = $canAssignRoles ? collect() : SchoolAccess::userAssignmentTeacherQuery()->orderBy('name')->get();
        $requiresTeacher = $currentUser->role === User::ROLE_ADMIN;
        $selectedTeacherIds = $user->teachers()->pluck('teacher.id')->all();
        if ($selectedTeacherIds === [] && $user->teacher_id) {
            $selectedTeacherIds = [(int) $user->teacher_id];
        }

        return view('users.edit', compact('user', 'teachers', 'schools', 'canAssignRoles', 'requiresTeacher', 'selectedTeacherIds'));
    })->name('users.edit');

    Route::put('/users/{user}', function (Request $request, User $user) {
        $currentUser = Auth::user();
        abort_if($currentUser->role === User::ROLE_ADMIN && $user->role !== User::ROLE_USER, 403);
        $schoolId = SchoolAccess::schoolId();

        if ($schoolId !== null) {
            abort_unless(
                (int) $user->school_id === $schoolId
                    || $user->teacher?->school_id === $schoolId
                    || $user->teachers()->where('teacher.school_id', $schoolId)->exists(),
                404
            );
        }

        $teacherRequired = $currentUser->role === User::ROLE_ADMIN;
        if (! $request->exists('teacher_ids') && $request->filled('teacher_id')) {
            $request->merge(['teacher_ids' => [$request->input('teacher_id')]]);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'max:255'],
            'role' => $currentUser->role === User::ROLE_SUPERADMIN
                ? ['required', 'string', 'in:' . implode(',', User::roles())]
                : ['prohibited'],
            'school_id' => $currentUser->role === User::ROLE_SUPERADMIN
                ? ['required', 'integer', 'exists:schools,id']
                : ['prohibited'],
            'teacher_ids' => $teacherRequired ? ['required', 'array', 'min:1'] : ['nullable', 'array'],
            'teacher_ids.*' => ['required', 'integer', 'distinct', 'exists:teacher,id'],
        ]);

        if ($currentUser->role === User::ROLE_ADMIN) {
            $data['role'] = $user->role;
            $data['school_id'] = $schoolId;
        }
        $teacherIds = $currentUser->role === User::ROLE_SUPERADMIN ? [] : ($data['teacher_ids'] ?? []);
        unset($data['teacher_ids']);
        $targetIsAdmin = $data['role'] === User::ROLE_ADMIN;
        if ($teacherIds !== [] && $currentUser->role !== User::ROLE_SUPERADMIN) {
            SchoolAccess::validateTeacherAssignments($teacherIds, $targetIsAdmin);
        }
        if ($currentUser->role === User::ROLE_SUPERADMIN) {
            $data['teacher_id'] = null;
        } else {
            $data['teacher_id'] = $teacherIds[0] ?? null;
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        $user->teachers()->sync($teacherIds);

        return redirect()->route('users.index')->with('status', __('User updated successfully.'));
    })->name('users.update');

    Route::patch('/users/{user}/active', function (User $user) {
        $currentUser = Auth::user();
        abort_if($currentUser->is($user), 403, __('You cannot deactivate your own account.'));
        abort_if($currentUser->role === User::ROLE_ADMIN && $user->role !== User::ROLE_USER, 403);

        $schoolId = SchoolAccess::schoolId();
        if ($schoolId !== null) {
            abort_unless(
                (int) $user->school_id === $schoolId
                    || $user->teacher?->school_id === $schoolId
                    || $user->teachers()->where('teacher.school_id', $schoolId)->exists(),
                404
            );
        }

        $user->update(['is_active' => ! $user->is_active]);

        return redirect()->route('users.index')->with('status', $user->is_active
            ? __('User activated successfully.')
            : __('User deactivated successfully.'));
    })->name('users.active');

    // Group School routes
    Route::get('/group-school/create', [GroupSchoolController::class, 'create'])->name('group-school.create');
    Route::post('/group-school', [GroupSchoolController::class, 'store'])->name('group-school.store');
    Route::get('/group-school', [GroupSchoolController::class, 'index'])->name('group-school.index');
    Route::get('/group-school/{groupSchool}', [GroupSchoolController::class, 'show'])->name('group-school.show');
    Route::get('/group-school/{groupSchool}/edit', [GroupSchoolController::class, 'edit'])->name('group-school.edit');
    Route::put('/group-school/{groupSchool}', [GroupSchoolController::class, 'update'])->name('group-school.update');
    Route::delete('/group-school/{groupSchool}', [GroupSchoolController::class, 'destroy'])->name('group-school.destroy');

    // Address routes
    Route::get('/provinces', [\App\Http\Controllers\ProvinceController::class, 'index'])->name('provinces.index');
    Route::get('/provinces/create', [\App\Http\Controllers\ProvinceController::class, 'create'])->name('provinces.create');
    Route::post('/provinces', [\App\Http\Controllers\ProvinceController::class, 'store'])->name('provinces.store');
    Route::get('/provinces/{province}/districts/create', [\App\Http\Controllers\ProvinceController::class, 'createDistrict'])->name('provinces.districts.create');
    Route::post('/provinces/{province}/districts', [\App\Http\Controllers\ProvinceController::class, 'storeDistrict'])->name('provinces.districts.store');
    Route::get('/provinces/{id}', [\App\Http\Controllers\ProvinceController::class, 'show'])->name('provinces.show');
    Route::get('/provinces/{id}/edit', [\App\Http\Controllers\ProvinceController::class, 'edit'])->name('provinces.edit');
    Route::put('/provinces/{id}', [\App\Http\Controllers\ProvinceController::class, 'update'])->name('provinces.update');
    Route::delete('/provinces/{id}', [\App\Http\Controllers\ProvinceController::class, 'destroy'])->name('provinces.destroy');
    Route::get('/districts/{district}/communes', [\App\Http\Controllers\ComController::class, 'showDistrict'])->name('districts.communes.show');
    Route::get('/districts/{district}/communes/create', [\App\Http\Controllers\ComController::class, 'createForDistrict'])->name('districts.communes.create');
    Route::post('/districts/{district}/communes', [\App\Http\Controllers\ComController::class, 'storeForDistrict'])->name('districts.communes.store');
    Route::get('/communes/{commune}', [\App\Http\Controllers\ComController::class, 'showCommune'])->name('communes.show');
    Route::get('/communes/{commune}/edit', [\App\Http\Controllers\ComController::class, 'editCommune'])->name('communes.edit');
    Route::put('/communes/{commune}', [\App\Http\Controllers\ComController::class, 'updateCommune'])->name('communes.update');
    Route::delete('/communes/{commune}', [\App\Http\Controllers\ComController::class, 'destroyCommune'])->name('communes.destroy');
    Route::get('/communes/{commune}/villages/create', [\App\Http\Controllers\villController::class, 'createForCommune'])->name('communes.villages.create');
    Route::post('/communes/{commune}/villages', [\App\Http\Controllers\villController::class, 'storeForCommune'])->name('communes.villages.store');

    // School routes
    Route::get('/schools', [\App\Http\Controllers\SchoolController::class, 'index'])->name('schools.index');
    Route::get('/schools/create', [\App\Http\Controllers\SchoolController::class, 'create'])->name('schools.create');
    Route::post('/schools', [\App\Http\Controllers\SchoolController::class, 'store'])->name('schools.store');
    Route::get('/schools/{school}', [\App\Http\Controllers\SchoolController::class, 'show'])->name('schools.show');
    Route::get('/schools/{school}/edit', [\App\Http\Controllers\SchoolController::class, 'edit'])->name('schools.edit');
    Route::put('/schools/{school}', [\App\Http\Controllers\SchoolController::class, 'update'])->name('schools.update');
    Route::delete('/schools/{school}', [\App\Http\Controllers\SchoolController::class, 'destroy'])->name('schools.destroy');

    // Teacher routes
    Route::get('/teachers', [\App\Http\Controllers\TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [\App\Http\Controllers\TeacherController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [\App\Http\Controllers\TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{teacher}', [\App\Http\Controllers\TeacherController::class, 'show'])->name('teachers.show');
    Route::get('/teachers/{teacher}/edit', [\App\Http\Controllers\TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{teacher}', [\App\Http\Controllers\TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [\App\Http\Controllers\TeacherController::class, 'destroy'])->name('teachers.destroy');

    // Year routes
    Route::get('/years', [\App\Http\Controllers\YearController::class, 'index'])->name('years.index');
    Route::get('/years/create', [\App\Http\Controllers\YearController::class, 'create'])->name('years.create');
    Route::post('/years', [\App\Http\Controllers\YearController::class, 'store'])->name('years.store');
    Route::get('/years/{year}', [\App\Http\Controllers\YearController::class, 'show'])->name('years.show');
    Route::get('/years/{year}/edit', [\App\Http\Controllers\YearController::class, 'edit'])->name('years.edit');
    Route::put('/years/{year}', [\App\Http\Controllers\YearController::class, 'update'])->name('years.update');
    Route::patch('/years/{year}/active', [\App\Http\Controllers\YearController::class, 'toggleActive'])->name('years.toggle-active');

    // Census routes
    Route::get('/census', [\App\Http\Controllers\CensusController::class, 'index'])->name('census.index');
    Route::post('/census/carry-over', [\App\Http\Controllers\CensusController::class, 'carryOver'])->name('census.carry-over');
    Route::get('/census/create', [\App\Http\Controllers\CensusController::class, 'create'])->name('census.create');
    Route::post('/census', [\App\Http\Controllers\CensusController::class, 'store'])->name('census.store');
    Route::get('/census/{census}', [\App\Http\Controllers\CensusController::class, 'show'])->name('census.show');
    Route::get('/census/{census}/edit', [\App\Http\Controllers\CensusController::class, 'edit'])->name('census.edit');
    Route::put('/census/{census}', [\App\Http\Controllers\CensusController::class, 'update'])->name('census.update');
    Route::delete('/census/{census}', [\App\Http\Controllers\CensusController::class, 'destroy'])->name('census.destroy');
});
