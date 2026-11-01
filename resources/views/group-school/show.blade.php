@extends('layouts.app')

@section('title', 'Group School Details')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $groupSchool->name }}</h1>
            <p class="text-secondary mb-0">Group school details and schools in this group.</p>
        </div>
        <a href="{{ route('group-school.edit', $groupSchool) }}" class="btn btn-primary">Edit Group School</a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Director</dt>
                <dd class="col-sm-9">{{ $groupSchool->director_name }}</dd>
                <dt class="col-sm-3">Phone</dt>
                <dd class="col-sm-9">{{ $groupSchool->phone }}</dd>
                <dt class="col-sm-3">Address</dt>
                <dd class="col-sm-9">{{ $groupSchool->address }}</dd>
            </dl>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><strong>Schools in this group</strong></div>
        <div class="card-body p-0">
            @if ($groupSchool->schools->isEmpty())
                <div class="p-4 text-center text-secondary">No schools are assigned to this group.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>School Name</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Director</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupSchool->schools as $school)
                                <tr>
                                    <td>{{ $school->name }}</td>
                                    <td>{{ $school->address ?? '-' }}</td>
                                    <td>{{ $school->phone ?? '-' }}</td>
                                    <td>{{ $school->email ?? '-' }}</td>
                                    <td>{{ $school->director_name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <a href="{{ route('group-school.index') }}" class="btn btn-link px-0 mt-3">Back to Group Schools</a>
</div>
@endsection
