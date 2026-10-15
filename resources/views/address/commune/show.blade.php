@extends('layouts.app')

@section('title', $commune->name . ' Village Details')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">{{ $commune->name }}</h1>
                <p class="text-secondary mb-0">Villages in this commune.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('districts.communes.create', $commune->district_id) }}" class="btn btn-outline-primary">Add Commune</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        <div class="card border-0 shadow-sm">
            @if ($commune->villages->isEmpty())
                <div class="list-group-item px-4 py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <span class="text-secondary">No villages found for this commune.</span>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('communes.villages.create', $commune) }}" class="btn btn-sm btn-primary">Add Village</a>
                        <a href="{{ route('communes.edit', $commune) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form
                            action="{{ route('communes.destroy', $commune) }}"
                            method="POST"
                            onsubmit="return confirm('Delete this commune and all its villages?');"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            @else
                <ul class="list-group list-group-flush">
                    @foreach ($commune->villages as $village)
                        <li class="list-group-item px-4 py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                            <span>{{ $village->name }}</span>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('communes.villages.create', $commune) }}" class="btn btn-sm btn-primary">Add Village</a>
                                <a href="{{ route('communes.edit', $commune) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form
                                    action="{{ route('communes.destroy', $commune) }}"
                                    method="POST"
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
