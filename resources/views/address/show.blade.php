@extends('layouts.app')

@section('title', $province->name . ' Districts')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">{{ $province->name }}</h1>
                <p class="text-secondary mb-0">Districts in this province.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('provinces.index') }}" class="btn btn-outline-secondary">Back to Provinces</a>
                <a href="{{ route('provinces.districts.create', $province) }}" class="btn btn-primary">Add District</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        <div class="card border-0 shadow-sm">
            @if ($province->districts->isEmpty())
                <div class="card-body p-4 text-center text-secondary">
                    No districts found for this province.
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach ($province->districts as $district)
                        <div class="list-group-item px-4 py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                            <span>{{ $district->name }}</span>
                            <div class="d-flex gap-2">
                                <a href="{{ route('districts.communes.show', $district) }}" class="btn btn-sm btn-outline-primary">Details</a>
                                <a href="{{ route('districts.communes.create', $district) }}" class="btn btn-sm btn-primary">Add Commune</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
