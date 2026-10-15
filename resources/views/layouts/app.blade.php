<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Census Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="app-shell d-flex">
        <aside class="app-sidebar d-flex flex-column flex-shrink-0 p-3" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="app-brand mb-4">Census Admin</a>

            <nav class="nav nav-pills flex-column gap-1">
                <a
                    href="{{ route('home') }}"
                    class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                    @if (request()->routeIs('home')) aria-current="page" @endif
                >Dashboard</a>
                <a
                    href="{{ route('group-school.index') }}"
                    class="nav-link {{ request()->routeIs('group-school.index', 'group-school.edit') ? 'active' : '' }}"
                    @if (request()->routeIs('group-school.index', 'group-school.edit')) aria-current="page" @endif
                >Group Schools</a>
                <a
                    href="{{ route('group-school.create') }}"
                    class="nav-link {{ request()->routeIs('group-school.create') ? 'active' : '' }}"
                    @if (request()->routeIs('group-school.create')) aria-current="page" @endif
                >Add Group School</a>
                <a
                    href="{{ route('provinces.index') }}"
                    class="nav-link {{ request()->routeIs('provinces.index', 'provinces.show') ? 'active' : '' }}"
                    @if (request()->routeIs('provinces.index', 'provinces.show')) aria-current="page" @endif
                >Address</a>
                
                <a
                    href="{{ route('users.create') }}"
                    class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}"
                    @if (request()->routeIs('users.create')) aria-current="page" @endif
                >Create User</a>
            </nav>

            <div class="mt-auto pt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light w-100">Log out</button>
                </form>
            </div>
        </aside>

        <main class="app-main flex-grow-1 p-3 p-md-4 p-xl-5">
            @yield('content')
        </main>
    </div>
</body>
</html>
