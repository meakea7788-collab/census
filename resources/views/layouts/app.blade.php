<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('Census Admin'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @if (app()->getLocale() === 'km')
        <style>body { font-family: "Noto Sans Khmer", "Khmer OS System", sans-serif; }</style>
    @endif
</head>
<body>
    <div class="app-shell d-flex">
        <aside class="app-sidebar d-flex flex-column flex-shrink-0 p-3" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="app-brand mb-4">
                {{ auth()->user()?->name ?? __('Census Admin') }}
            </a>

            <nav class="nav nav-pills flex-column gap-1">
                <a
                    href="{{ route('home') }}"
                    class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                    @if (request()->routeIs('home')) aria-current="page" @endif
                >{{ __('Dashboard') }}</a>
                <a
                    href="{{ route('hole_houses.index') }}"
                    class="nav-link {{ request()->routeIs('hole_houses.*') ? 'active' : '' }}"
                    @if (request()->routeIs('hole_houses.*')) aria-current="page" @endif
                >{{ __('Hole House') }}</a>
                @if (\Illuminate\Support\Facades\Route::has('family.index'))
                    <a
                        href="{{ route('family.index') }}"
                        class="nav-link {{ request()->routeIs('family.*') ? 'active' : '' }}"
                        @if (request()->routeIs('family.*')) aria-current="page" @endif
                    >{{ __('Families') }}</a>
                @endif

                @if (in_array(auth()->user()?->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true))
                    <a
                        href="{{ route('census.index') }}"
                        class="nav-link {{ request()->routeIs('census.index', 'census.create') ? 'active' : '' }}"
                        @if (request()->routeIs('census.index', 'census.create')) aria-current="page" @endif
                    >{{ __('Census') }}</a>
                    <a
                        href="{{ route('group-school.index') }}"
                        class="nav-link {{ request()->routeIs('group-school.index', 'group-school.edit') ? 'active' : '' }}"
                        @if (request()->routeIs('group-school.index', 'group-school.edit')) aria-current="page" @endif
                    >{{ __('Group Schools') }}</a>
                    <a
                        href="{{ route('years.index') }}"
                        class="nav-link {{ request()->routeIs('years.index', 'years.edit') ? 'active' : '' }}"
                        @if (request()->routeIs('years.index', 'years.edit')) aria-current="page" @endif
                    >{{ __('Years') }}</a>
                    <a
                        href="{{ route('schools.index') }}"
                        class="nav-link {{ request()->routeIs('schools.index', 'schools.edit') ? 'active' : '' }}"
                        @if (request()->routeIs('schools.index', 'schools.edit')) aria-current="page" @endif
                    >{{ __('Schools') }}</a>
                    <a
                        href="{{ route('teachers.index') }}"
                        class="nav-link {{ request()->routeIs('teachers.index', 'teachers.edit') ? 'active' : '' }}"
                        @if (request()->routeIs('teachers.index', 'teachers.edit')) aria-current="page" @endif  
                    >{{ __('Teachers') }}</a>
                    @if (auth()->user()?->role === \App\Models\User::ROLE_SUPERADMIN)
                        <a
                            href="{{ route('group-school.create') }}"
                            class="nav-link {{ request()->routeIs('group-school.create') ? 'active' : '' }}"
                            @if (request()->routeIs('group-school.create')) aria-current="page" @endif
                        >{{ __('Create Group School') }}</a>
                    @endif
                    <a
                        href="{{ route('provinces.index') }}"
                        class="nav-link {{ request()->routeIs('provinces.index', 'provinces.show') ? 'active' : '' }}"
                        @if (request()->routeIs('provinces.index', 'provinces.show')) aria-current="page" @endif
                    >{{ __('Address') }}</a>
                    
                    <a
                        href="{{ route('users.index') }}"
                        class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
                        @if (request()->routeIs('users.index')) aria-current="page" @endif
                    >{{ __('Users') }}</a>
                    <a
                        href="{{ route('users.create') }}"
                        class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}"
                        @if (request()->routeIs('users.create')) aria-current="page" @endif
                    >{{ __('Create User') }}</a>
                @endif

                <a
                    href="{{ route('profile') }}"
                    class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}"
                    @if (request()->routeIs('profile')) aria-current="page" @endif
                >{{ __('My Profile') }}</a>
            </nav>

            <form method="POST" action="{{ route('language.switch') }}" class="mt-3">
                @csrf
                <label for="locale" class="form-label text-white">{{ __('Language') }}</label>
                <select id="locale" name="locale" class="form-select" onchange="this.form.submit()" aria-label="{{ __('Language') }}">
                    <option value="km" @selected(app()->getLocale() === 'km')>ភាសាខ្មែរ</option>
                    <option value="en" @selected(app()->getLocale() === 'en')>English</option>
                </select>
            </form>

            <div class="mt-auto pt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light w-100">{{ __('Log out') }}</button>
                </form>
            </div>
        </aside>

        <main class="app-main flex-grow-1 p-3 p-md-4 p-xl-5">
            @yield('content')
        </main>
    </div>
</body>
</html>
