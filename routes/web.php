<?php

use App\Http\Controllers\GroupSchoolController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome-page');
})->name('welcome');

Route::get('/home', function () {
    return view('dashboard');
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/logout', function (Request $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('welcome');
})->name('logout');

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('status', 'Login successful!');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login.submit');

Route::get('/users/create', function () {
    return view('users.create');
})->name('users.create');

Route::post('/users/store', function (Request $request) {
    $allowedRoles = implode(',', User::roles());

    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['nullable', 'email', 'max:255'],
        'password' => ['nullable', 'string', 'min:6', 'max:255'],
        'role' => ['nullable', 'string', 'in:' . $allowedRoles],
    ]);

    $data['email'] = $data['email'] ?? strtolower(Str::slug($data['name'], '') ?: 'user') . uniqid() . '@example.com';
    $data['password'] = $data['password'] ?? 'password123';
    $data['role'] = $data['role'] ?? User::ROLE_USER;

    User::create($data);

    return redirect()->route('users.create')->with('status', 'User created successfully.');
})->name('users.store');

Route::get('/group-school/create', [GroupSchoolController::class, 'create'])->name('group-school.create');
Route::post('/group-school', [GroupSchoolController::class, 'store'])->name('group-school.store');
Route::get('/group-school', [GroupSchoolController::class, 'index'])->name('group-school.index');
Route::get('/group-school/{groupSchool}/edit', [GroupSchoolController::class, 'edit'])->name('group-school.edit');
Route::put('/group-school/{groupSchool}', [GroupSchoolController::class, 'update'])->name('group-school.update');
Route::delete('/group-school/{groupSchool}', [GroupSchoolController::class, 'destroy'])->name('group-school.destroy');

//address routes
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