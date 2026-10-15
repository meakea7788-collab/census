@extends('layouts.app')

@section('title', 'Group Schools')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Group Schools</h1>
                <p class="text-secondary mb-0">View and manage registered group schools.</p>
            </div>
            <a href="{{ route('group-school.create') }}" class="btn btn-primary">Add Group School</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if ($groupSchools->isEmpty())
                    <div class="p-4 text-center text-secondary">
                        No group schools found.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Group School</th>
                                    <th scope="col">Director</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Address</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupSchools as $school)
                                    <tr>
                                        <td>{{ $school->name }}</td>
                                        <td>{{ $school->director_name }}</td>
                                        <td>{{ $school->phone }}</td>
                                        <td>{{ $school->address }}</td>
                                        <td class="text-end text-nowrap">
                                            <a href="{{ route('group-school.edit', $school) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form action="{{ route('group-school.destroy', $school) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this group school?');">
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

        <a href="{{ route('home') }}" class="btn btn-link px-0 mt-3">Back to Dashboard</a>
    </div>
@endsection
