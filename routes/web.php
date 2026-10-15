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
