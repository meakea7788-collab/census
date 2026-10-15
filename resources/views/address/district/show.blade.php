@extends('layouts.app')

@section('title', $district->name . ' Communes')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">{{ $district->name }}</h1>
                <p class="text-secondary mb-0">Communes in this district.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('provinces.show', $district->province_id) }}" class="btn btn-outline-secondary">Back to Districts</a>
                <a href="{{ route('districts.communes.create', $district) }}" class="btn btn-primary">Add Commune</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        <div class="card border-0 shadow-sm">
            @if ($district->communes->isEmpty())
                <div class="card-body p-4 text-center text-secondary">No communes found for this district.</div>
            @else
                <ul class="list-group list-group-flush">
                    @foreach ($district->communes as $commune)
                        <li class="list-group-item px-4 py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                            <span>{{ $commune->name }}</span>
                            <div class="d-flex gap-2">
                                <a href="{{ route('communes.show', $commune) }}" class="btn btn-sm btn-outline-info">Details</a>
                                <a href="{{ route('communes.edit', $commune) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form
                                    action="{{ route('communes.destroy', $commune) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete this commune and all its villages?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
