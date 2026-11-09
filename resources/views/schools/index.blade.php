@extends('layouts.app')

@section('content')
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1>{{ __('Schools') }}</h1>
		<a href="{{ route('schools.create') }}" class="btn btn-primary">{{ __('Create School') }}</a>
	</div>

	@if (session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif

	<div class="table-responsive">
		<table class="table table-striped table-bordered align-middle">
			<thead>
				<tr>
					<th>{{ __('Name') }}</th>
					<th>{{ __('Address') }}</th>
					<th>{{ __('Village in School') }}</th>
					<th>{{ __('Phone') }}</th>
					<th>{{ __('Email') }}</th>
					<th>{{ __('Group School') }}</th>
					<th width="220">{{ __('Actions') }}</th>
				</tr>
			</thead>
			<tbody>
				@forelse ($schools as $school)
					<tr>
						<td>{{ $school->name }}</td>
						<td>{{ $school->address ?? '—' }}</td>
						<td>{{ $school->villages->pluck('name')->join(', ') ?: ($school->village ?? '—') }}</td>
						<td>{{ $school->phone ?? '—' }}</td>
						<td>{{ $school->email ?? '—' }}</td>
						<td>{{ $school->groupSchool?->name ?? '—' }}</td>
						<td class="text-nowrap">
							<div class="d-flex gap-2 flex-wrap">
								<a href="{{ route('schools.show', $school) }}" class="btn btn-sm btn-info text-white" title="{{ __('Detail') }}" aria-label="{{ __('Detail') }}">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8ZM1.17 8a13.1 13.1 0 0 1 1.66-2.1C4.16 4.5 5.91 3.5 8 3.5s3.84 1 5.17 2.4A13.1 13.1 0 0 1 14.83 8a13.1 13.1 0 0 1-1.66 2.1C11.84 11.5 10.09 12.5 8 12.5s-3.84-1-5.17-2.4A13.1 13.1 0 0 1 1.17 8Z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Zm-1.5 2.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z"/></svg>
								</a>
								<a href="{{ route('schools.edit', $school) }}" class="btn btn-sm btn-primary" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M12.85.15a.5.5 0 0 1 .71 0l2.29 2.29a.5.5 0 0 1 0 .71l-9.5 9.5a.5.5 0 0 1-.22.13l-4 1a.5.5 0 0 1-.61-.61l1-4a.5.5 0 0 1 .13-.22l9.5-9.5ZM12.5 1.21 4.09 9.62l-.62 2.48 2.48-.62 8.41-8.41L12.5 1.21Z"/></svg>
								</a>
								<form action="{{ route('schools.destroy', $school) }}" method="POST" onsubmit="return confirm(@js(__('Are you sure you want to delete this school?')));">
									@csrf
									@method('DELETE')
									<button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M5.5 5.5a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9.5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1 0-2H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1ZM4 4v9.5a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4H4Zm2.5-2a.5.5 0 0 0-.5.5V3h4v-.5a.5.5 0 0 0-.5-.5h-3Z"/></svg>
									</button>
								</form>
							</div>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="6" class="text-center">{{ __('No schools found.') }}</td>
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
