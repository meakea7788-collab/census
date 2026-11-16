@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <div class="container-fluid p-0">
        <div class="mb-4">
            <h1 class="h3 mb-2">{{ __('Dashboard') }}</h1>
            <p class="text-secondary mb-0">
                @if (auth()->user()?->role === \App\Models\User::ROLE_USER)
                    {{ __('Welcome') }}, {{ auth()->user()?->name }} · {{ auth()->user()?->school?->name ?? auth()->user()?->teacher?->school?->name ?? __('Census User') }}
                @elseif (auth()->user()?->role === \App\Models\User::ROLE_ADMIN)
                    {{ __('Welcome to the Census Admin dashboard.') }} ({{ auth()->user()?->school?->name ?? auth()->user()?->teacher?->school?->name }})
                @else
                    {{ __('Welcome to the Census dashboard.') }}
                @endif
            </p>
        </div>

        @if (auth()->user()?->role === \App\Models\User::ROLE_USER)
            <div class="row g-3">
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h2 class="h5 card-title mb-0">{{ __('Hole House') }}</h2>
                                <span class="badge text-bg-primary fs-6">{{ $holeHousesCount ?? 0 }}</span>
                            </div>
                            <p class="card-text text-secondary mb-4 flex-grow-1">{{ __('View and manage hole house records.') }}</p>
                            <div>
                                <a href="{{ route('hole_houses.index') }}" class="btn btn-primary">{{ __('View Hole Houses') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                @if (\Illuminate\Support\Facades\Route::has('family.index'))
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h2 class="h5 card-title mb-0">{{ __('Families') }}</h2>
                                    <span class="badge text-bg-primary fs-6">{{ $familiesCount ?? 0 }}</span>
                                </div>
                                <p class="card-text text-secondary mb-4 flex-grow-1">{{ __('View and manage family member records.') }}</p>
                                <div>
                                    <a href="{{ route('family.index') }}" class="btn btn-primary">{{ __('View Families') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 d-flex flex-column">
                            <h2 class="h5 card-title mb-2">{{ __('My Profile') }}</h2>
                            <p class="card-text text-secondary mb-4 flex-grow-1">{{ __('View your account details and assigned school.') }}</p>
                            <div>
                                <a href="{{ route('profile') }}" class="btn btn-outline-primary">{{ __('View Profile') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 card-title">{{ __('Quick links') }}</h2>
                    <p class="card-text text-secondary">{{ __('Manage group schools or add a new group school.') }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('group-school.index') }}" class="btn btn-primary">{{ __('View Group Schools') }}</a>
                        @if (auth()->user()?->role === \App\Models\User::ROLE_SUPERADMIN)
                            <a href="{{ route('group-school.create') }}" class="btn btn-outline-primary">{{ __('Add Group School') }}</a>
                        @endif
                        <a href="{{ route('hole_houses.index') }}" class="btn btn-outline-secondary">
                            {{ __('View Hole Houses') }} <span class="badge text-bg-secondary ms-1">{{ $holeHousesCount ?? 0 }}</span>
                        </a>
                        @if (\Illuminate\Support\Facades\Route::has('family.index'))
                            <a href="{{ route('family.index') }}" class="btn btn-outline-secondary">
                                {{ __('View Families') }} <span class="badge text-bg-secondary ms-1">{{ $familiesCount ?? 0 }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-4">
            <h2 class="h5 mb-3">{{ __('Village Statistics') }}</h2>
            <div class="row g-3">
                @forelse ($villageStats ?? [] as $villageStat)
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                                    <h3 class="h5 card-title mb-0 fw-bold text-primary">
                                        {{ __('Village') }} {{ $villageStat->name }}
                                    </h3>
                                    <span class="badge text-bg-primary rounded-pill">
                                        {{ $villageStat->houses_count }} {{ __('Hole Houses') }}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center py-1">
                                        <span class="text-secondary">{{ __('Amount of Hole Houses') }}</span>
                                        <span class="fw-bold fs-6">{{ $villageStat->houses_count }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-1">
                                        <span class="text-secondary">{{ __('Family Members') }} ({{ __('Total / Female') }})</span>
                                        <span class="fw-bold fs-6">
                                            {{ $villageStat->total_members }} / <span class="text-danger">{{ $villageStat->female_members }}</span>
                                        </span>
                                    </div>
                                </div>

                                <div class="bg-light rounded-3 p-3 mb-3 small flex-grow-1">
                                    <div class="row g-2">
                                        <div class="col-6 d-flex justify-content-between">
                                            <span class="text-secondary">{{ __('Male') }}:</span>
                                            <span class="fw-semibold">{{ $villageStat->male_members }}</span>
                                        </div>
                                        <div class="col-6 d-flex justify-content-between">
                                            <span class="text-secondary">{{ __('Female') }}:</span>
                                            <span class="fw-semibold text-danger">{{ $villageStat->female_members }}</span>
                                        </div>
                                        <div class="col-6 d-flex justify-content-between">
                                            <span class="text-secondary">{{ __('Occupied') }}:</span>
                                            <span class="fw-semibold text-success">{{ $villageStat->occupied_houses }}</span>
                                        </div>
                                        <div class="col-6 d-flex justify-content-between">
                                            <span class="text-secondary">{{ __('Vacant') }}:</span>
                                            <span class="fw-semibold">{{ $villageStat->vacant_houses }}</span>
                                        </div>
                                        <div class="col-6 d-flex justify-content-between">
                                            <span class="text-secondary">{{ __('Moved') }}:</span>
                                            <span class="fw-semibold text-info">{{ $villageStat->moved_members }}</span>
                                        </div>
                                        <div class="col-6 d-flex justify-content-between">
                                            <span class="text-secondary">{{ __('Deceased') }}:</span>
                                            <span class="fw-semibold text-secondary">{{ $villageStat->deceased_members }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-auto">
                                    <a href="{{ route('hole_houses.index', ['village_id' => $villageStat->id]) }}" class="btn btn-sm btn-outline-primary w-100">
                                        {{ __('View Hole Houses') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center text-secondary py-4">
                                {{ __('No villages found.') }}
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
