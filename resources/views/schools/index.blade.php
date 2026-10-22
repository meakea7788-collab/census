@extends('layouts.app')

@section('content')
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1>Schools</h1>
		<a href="{{ route('schools.create') }}" class="btn btn-primary">Create School</a>
	</div>

	@if (session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif

	<div class="table-responsive">
		<table class="table table-striped table-bordered align-middle">
			<thead>
				<tr>
					<th>Name</th>
					<th>Address</th>
					<th>Phone</th>
					<th>Email</th>
					<th>Group School</th>
					<th>Created</th>
					<th width="220">Actions</th>
				</tr>
			</thead>
			<tbody>
				@forelse ($schools as $school)
					<tr>
						<td>{{ $school->name }}</td>
						<td>{{ $school->address ?? '—' }}</td>
						<td>{{ $school->phone ?? '—' }}</td>
						<td>{{ $school->email ?? '—' }}</td>
						<td>{{ $school->groupSchool?->name ?? '—' }}</td>
						<td>{{ $school->created_at?->format('M j, Y') ?? '—' }}</td>
						<td>
							<div class="d-flex gap-2 flex-wrap">
								<a href="{{ route('schools.show', $school) }}" class="btn btn-sm btn-info text-white">Detail</a>
								<a href="{{ route('schools.edit', $school) }}" class="btn btn-sm btn-primary">Edit</a>
								<form action="{{ route('schools.destroy', $school) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this school?');">
									@csrf
									@method('DELETE')
									<button type="submit" class="btn btn-sm btn-danger">Delete</button>
								</form>
							</div>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="7" class="text-center">No schools found.</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>

	@if (method_exists($schools, 'links'))
		{{ $schools->links() }}
	@endif
</div>
@endsection
