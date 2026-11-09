@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('School Details') }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('schools.edit', $school) }}" class="btn btn-primary">{{ __('Edit') }}</a>
            <a href="{{ route('schools.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('Name') }}</dt>
                <dd class="col-sm-9">{{ $school->name }}</dd>

                <dt class="col-sm-3">{{ __('Address') }}</dt>
                <dd class="col-sm-9">{{ $school->address ?? '—' }}</dd>

                <dt class="col-sm-3">{{ __('Villages') }}</dt>
                <dd class="col-sm-9">
                    @if ($school->villages->isNotEmpty())
                        {{ $school->villages->pluck('name')->join(', ') }}
                    @else
                        {{ $school->village ?? '—' }}
                    @endif
                </dd>

                <dt class="col-sm-3">{{ __('Phone') }}</dt>
                <dd class="col-sm-9">{{ $school->phone ?? '—' }}</dd>

                <dt class="col-sm-3">{{ __('Email') }}</dt>
                <dd class="col-sm-9">{{ $school->email ?? '—' }}</dd>

                <dt class="col-sm-3">{{ __('Director Name') }}</dt>
                <dd class="col-sm-9">{{ $school->director_name ?? '—' }}</dd>

                <dt class="col-sm-3">{{ __('Group School') }}</dt>
                <dd class="col-sm-9">{{ $school->groupSchool?->name ?? '—' }}</dd>

                <dt class="col-sm-3">{{ __('Created At') }}</dt>
                <dd class="col-sm-9">{{ $school->created_at?->format('M j, Y H:i') ?? '—' }}</dd>
            </dl>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>{{ __('Teachers') }}</strong>
            <a href="{{ route('teachers.create', ['school_id' => $school->id]) }}" class="btn btn-primary btn-sm">
                {{ __('Add Teacher') }}
            </a>
        </div>
        <div class="card-body">
            @if ($school->teachers->isEmpty())
                <p class="text-secondary mb-0">{{ __('No teachers assigned to this school.') }}</p>
            @else
                <ul class="list-group list-group-flush">
                    @foreach ($school->teachers as $teacher)
                        <li class="list-group-item px-0">{{ $teacher->name }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
