@extends('layouts.app')

@section('content')
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1>Teachers</h1>
		<a href="{{ route('teachers.create') }}" class="btn btn-primary">Add Teacher</a>
	</div>

	@if (session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif

	<div class="table-responsive">
		<table class="table table-striped table-bordered align-middle">
			<thead>
				<tr>
					<th>Name</th>
					<th>Sex</th>
					<th>Position</th>
					<th>Phone</th>
					<th>School</th>
                    <th width="220">Actions</th>
				</tr>
			</thead>
			<tbody>
				@forelse ($teachers as $teacher)
					<tr>
						<td>{{ $teacher->name }}</td>
						<td>{{ $teacher->sex }}</td>
						<td>{{ $teacher->position }}</td>
						<td>{{ $teacher->phone }}</td>
						<td>{{ $teacher->school?->name ?? '—' }}</td>
                        <td>
                            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this teacher?')">Delete</button>
                            </form>
                        </td>
					</tr>
				@empty
					<tr>
						<td colspan="6" class="text-center">No teachers found.</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
@endsection
