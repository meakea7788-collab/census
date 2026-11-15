@extends('layouts.app')

@section('title', __('My Profile'))

@section('content')
    <div class="container-fluid p-0">
        <div class="mb-4">
            <h1 class="h3 mb-1">{{ __('My Profile') }}</h1>
            <p class="text-secondary mb-0">{{ __('Your account details.') }}</p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-semibold"
                         style="width: 56px; height: 56px;" aria-hidden="true">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="h5 mb-1">{{ $user->name }}</h2>
                        <span class="badge text-bg-secondary">{{ __($user->role) }}</span>
                    </div>
                </div>

                <dl class="row mb-0">
                    <dt class="col-sm-3">{{ __('Name') }}</dt>
                    <dd class="col-sm-9">{{ $user->name }}</dd>

                    <dt class="col-sm-3">{{ __('Email') }}</dt>
                    <dd class="col-sm-9">{{ $user->email }}</dd>

                    <dt class="col-sm-3">{{ __('Role') }}</dt>
                    <dd class="col-sm-9">{{ __($user->role) }}</dd>

                    <dt class="col-sm-3">{{ __('Status') }}</dt>
                    <dd class="col-sm-9">
                        <span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ $user->is_active ? __('Active') : __('Inactive') }}
                        </span>
                    </dd>

                    <dt class="col-sm-3">{{ __('School') }}</dt>
                    <dd class="col-sm-9">{{ $user->school?->name ?? $user->teacher?->school?->name ?? '—' }}</dd>

                    <dt class="col-sm-3">{{ __('Teachers') }}</dt>
                    <dd class="col-sm-9">
                        @forelse ($user->teachers as $teacher)
                            <span class="d-block">{{ $teacher->id }} · {{ $teacher->name }}</span>
                            <span class="d-block small text-secondary">{{ $teacher->school?->groupSchool?->name ?? '—' }}</span>
                        @empty
                            @if ($user->teacher)
                                {{ $user->teacher_id }} · {{ $user->teacher->name }}
                            @else
                                —
                            @endif
                        @endforelse
                    </dd>

                    @if ($user->teacher || $user->teachers->isNotEmpty())
                        <dt class="col-sm-3">{{ __('Group School') }}</dt>
                        <dd class="col-sm-9">
                            @forelse ($user->teachers as $teacher)
                                <span class="d-block">{{ $teacher->name }} — {{ $teacher->school?->groupSchool?->name ?? '—' }}</span>
                            @empty
                                {{ $user->teacher?->school?->groupSchool?->name ?? '—' }}
                            @endforelse
                        </dd>
                    @endif

                    <dt class="col-sm-3">{{ __('Account created') }}</dt>
                    <dd class="col-sm-9">{{ $user->created_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
@endsection