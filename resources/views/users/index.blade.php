@extends('layouts.app')

@section('title', __('Users'))

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">{{ __('Users') }}</h1>
                <p class="text-secondary mb-0">{{ __('View registered user accounts.') }}</p>
            </div>
            @if (in_array(auth()->user()?->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true))
                <a href="{{ route('users.create') }}" class="btn btn-primary">{{ __('Create User') }}</a>
            @endif
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if ($users->isEmpty())
                    <div class="p-4 text-center text-secondary">{{ __('No users found.') }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Email') }}</th>
                                    <th scope="col">{{ __('School') }}</th>
                                    <th scope="col">{{ __('Teacher ID') }}</th>
                                    <th scope="col">{{ __('Role') }}</th>
                                    <th scope="col">{{ __('Status') }}</th>
                                    <th scope="col">{{ __('Created At') }}</th>
                                    @if (in_array(auth()->user()?->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true))
                                        <th scope="col">{{ __('Actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="fw-semibold">{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->school?->name ?? $user->teacher?->school?->name ?? '—' }}</td>
                                        <td>
                                            @if ($user->teachers->isNotEmpty())
                                                @foreach ($user->teachers as $teacher)
                                                    <span class="d-block">{{ $teacher->id }} · {{ $teacher->name }}</span>
                                                    <span class="d-block small text-secondary">{{ $teacher->school?->groupSchool?->name ?? '—' }}</span>
                                                @endforeach
                                            @elseif ($user->teacher_id)
                                                <span>{{ $user->teacher_id }} · {{ $user->teacher?->name }}</span>
                                                <span class="d-block small text-secondary">{{ $user->teacher?->school?->groupSchool?->name ?? '—' }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge text-bg-secondary">{{ __($user->role) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                                {{ $user->is_active ? __('Active') : __('Inactive') }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at?->format('Y-m-d') ?? '—' }}</td>
                                        @if (in_array(auth()->user()?->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true))
                                            <td class="text-nowrap">
                                                @if (auth()->user()?->role === \App\Models\User::ROLE_SUPERADMIN || $user->role === \App\Models\User::ROLE_USER)
                                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                                        {{ __('Edit') }}
                                                    </a>
                                                @endif
                                                @if (! auth()->user()->is($user) && (auth()->user()?->role === \App\Models\User::ROLE_SUPERADMIN || $user->role === \App\Models\User::ROLE_USER))
                                                    <form action="{{ route('users.active', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                                            {{ $user->is_active ? __('Deactivate') : __('Activate') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection