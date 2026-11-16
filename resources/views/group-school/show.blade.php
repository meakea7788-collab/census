@extends('layouts.app')

@section('title', __('Group School Details'))

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $groupSchool->name }}</h1>
            <p class="text-secondary mb-0">{{ __('Group school details and schools in this group.') }}</p>
        </div>
        @if (auth()->user()?->role !== \App\Models\User::ROLE_ADMIN)
            <a href="{{ route('group-school.edit', $groupSchool) }}" class="btn btn-primary">{{ __('Edit Group School') }}</a>
        @endif
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('Director') }}</dt>
                <dd class="col-sm-9">{{ $groupSchool->director_name }}</dd>
                <dt class="col-sm-3">{{ __('Phone') }}</dt>
                <dd class="col-sm-9">{{ $groupSchool->phone }}</dd>
                <dt class="col-sm-3">{{ __('Address') }}</dt>
                <dd class="col-sm-9">{{ $groupSchool->address }}</dd>
            </dl>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>{{ __('Schools in this group') }}</strong>
            @if (auth()->user()?->role !== \App\Models\User::ROLE_ADMIN)
                <a href="{{ route('schools.create', ['group_school_id' => $groupSchool->id]) }}" class="btn btn-primary btn-sm">
                    {{ __('Create School') }}
                </a>
            @endif
        </div>
        <div class="card-body p-0">
            @if ($groupSchool->schools->isEmpty())
                <div class="p-4 text-center text-secondary">{{ __('No schools are assigned to this group.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('School Name') }}</th>
                                <th>{{ __('Address') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Director') }}</th>
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

    <a href="{{ route('group-school.index') }}" class="btn btn-link px-0 mt-3">{{ __('Back to Group Schools') }}</a>
</div>
@endsection
