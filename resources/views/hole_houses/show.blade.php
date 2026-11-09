@extends('layouts.app')

@section('title', __('Hole House Details'))

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ __('Hole House Details') }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('family.create', ['holehouse_id' => $holeHouse->id]) }}" class="btn btn-success">{{ __('Add family member') }}</a>
            <a href="{{ route('hole_houses.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('Hole number') }}</dt>
                <dd class="col-sm-9">{{ $holeHouse->hole_number }}</dd>

                <dt class="col-sm-3">{{ __('Household leader') }}</dt>
                <dd class="col-sm-9">{{ $holeHouse->leader_family_name }}</dd>

                <dt class="col-sm-3">{{ __('Phone number') }}</dt>
                <dd class="col-sm-9">{{ $holeHouse->phone_number }}</dd>

                <dt class="col-sm-3">{{ __('Village') }}</dt>
                <dd class="col-sm-9">{{ $holeHouse->village?->name ?? '—' }}</dd>

                <dt class="col-sm-3">{{ __('Additional address details') }}</dt>
                <dd class="col-sm-9">{{ $holeHouse->address ?: '—' }}</dd>

                <dt class="col-sm-3">{{ __('Status') }}</dt>
                <dd class="col-sm-9">{{ __($holeHouse->status) }}</dd>
            </dl>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h2 class="h4 mb-0">{{ __('Family members') }}</h2>
        <span class="badge text-bg-secondary">{{ $holeHouse->families->count() }}</span>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Family name') }}</th>
                        <th scope="col">{{ __('Gender') }}</th>
                        <th scope="col">{{ __('Date of birth') }}</th>
                        <th scope="col">{{ __('Relationship') }}</th>
                        <th scope="col">{{ __('Job') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($holeHouse->families as $familyMember)
                        <tr>
                            <td>{{ $familyMember->id }}</td>
                            <td>{{ $familyMember->family_name }}</td>
                            <td>{{ $familyMember->gender }}</td>
                            <td>{{ $familyMember->date_of_birth }}</td>
                            <td>{{ $familyMember->relationship }}</td>
                            <td>{{ $familyMember->job }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">{{ __('No family members have been added to this house yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
