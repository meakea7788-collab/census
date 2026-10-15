@extends('layouts.app')

@section('title', 'Provinces')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Provinces</h1>
                <p class="text-secondary mb-0">Manage provinces and view their districts.</p>
            </div>
            <a href="{{ route('provinces.create') }}" class="btn btn-primary">Create Province</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        <div class="card border-0 shadow-sm">
            @if ($provinces->isEmpty())
                <div class="card-body p-4 text-center text-secondary">
                    No provinces found. Create a province to get started.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Province</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($provinces as $province)
                                <tr>
                                    <td>{{ $province->name }}</td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('provinces.show', $province->id) }}" class="btn btn-sm btn-outline-info">Details</a>
                                        <a href="{{ route('provinces.edit', $province->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form
                                            action="{{ route('provinces.destroy', $province->id) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this province and all its districts, communes, and villages?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
