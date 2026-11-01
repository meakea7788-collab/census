@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>School Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('schools.edit', $school) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('schools.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $school->name }}</dd>

                <dt class="col-sm-3">Address</dt>
                <dd class="col-sm-9">{{ $school->address ?? '—' }}</dd>

                <dt class="col-sm-3">Phone</dt>
                <dd class="col-sm-9">{{ $school->phone ?? '—' }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $school->email ?? '—' }}</dd>

                <dt class="col-sm-3">Director Name</dt>
                <dd class="col-sm-9">{{ $school->director_name ?? '—' }}</dd>

                <dt class="col-sm-3">Group School</dt>
                <dd class="col-sm-9">{{ $school->groupSchool?->name ?? '—' }}</dd>

                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $school->created_at?->format('M j, Y H:i') ?? '—' }}</dd>
            </dl>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white"><strong>Teachers</strong></div>
        <div class="card-body">
            @if ($school->teachers->isEmpty())
                <p class="text-secondary mb-0">No teachers assigned to this school.</p>
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
