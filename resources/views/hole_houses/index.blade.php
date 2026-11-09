@extends('layouts.app')

@section('content')
<div class="container py-4">
	<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
		<h1 class="h3 mb-0">{{ __('Hole Houses') }}</h1>
		<div class="d-flex flex-wrap gap-2">
			<form method="GET" action="{{ route('hole_houses.index') }}" class="d-flex gap-2">
				<label for="house-search" class="visually-hidden">{{ __('Search by name, house number, or village') }}</label>
				<input
					id="house-search"
					type="search"
					name="search"
					value="{{ $search }}"
					class="form-control"
					placeholder="{{ __('Name, house number, or village') }}"
				>
				<button type="submit" class="btn btn-outline-primary">{{ __('Search') }}</button>
				@if ($search !== '')
					<a href="{{ route('hole_houses.index') }}" class="btn btn-outline-secondary">{{ __('Clear') }}</a>
				@endif
			</form>
			<a href="{{ route('hole_houses.create') }}" class="btn btn-primary">{{ __('Create Hole House') }}</a>
		</div>
	</div>

	@if (session('success'))
		<div class="alert alert-success" role="alert">{{ session('success') }}</div>
	@endif

	@if (isset($holeHouses) && $holeHouses->count())
		<div class="card">
			<div class="table-responsive">
				@php
					$columnLabels = [
						'id' => __('ID'),
						'hole_number' => __('House number'),
						'leader_family_name' => __('Household leader'),
						'phone_number' => __('Phone number'),
						'address' => __('Address'),
						'village_id' => __('Village'),
						'status' => __('Status'),
						'created_at' => __('Created At'),
						'updated_at' => __('Updated At'),
					];
				@endphp
				<table class="table table-striped mb-0">
					<thead>
						<tr>
							@foreach ($holeHouses->first()->getAttributes() as $column => $value)
								@if ($column !== 'census_id')
									<th scope="col">{{ $columnLabels[$column] ?? __(ucfirst(str_replace('_', ' ', $column))) }}</th>
								@endif
							@endforeach
							<th scope="col">{{ __('Actions') }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($holeHouses as $holeHouse)
							<tr>
								@foreach ($holeHouse->getAttributes() as $column => $value)
									@if ($column !== 'census_id')
										<td>{{ $column === 'status' ? __($value) : ($column === 'village_id' ? ($holeHouse->village?->name ?? '—') : $value) }}</td>
									@endif
								@endforeach
								<td class="text-nowrap">
									<a href="{{ route('hole_houses.show', $holeHouse->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Show') }}" aria-label="{{ __('Show') }}">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8ZM1.17 8a13.1 13.1 0 0 1 1.66-2.1C4.16 4.5 5.91 3.5 8 3.5s3.84 1 5.17 2.4A13.1 13.1 0 0 1 14.83 8a13.1 13.1 0 0 1-1.66 2.1C11.84 11.5 10.09 12.5 8 12.5s-3.84-1-5.17-2.4A13.1 13.1 0 0 1 1.17 8Z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Zm-1.5 2.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z"/></svg>
									</a>
									<a href="{{ route('hole_houses.edit', $holeHouse->id) }}" class="btn btn-sm btn-outline-warning" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M12.85.15a.5.5 0 0 1 .71 0l2.29 2.29a.5.5 0 0 1 0 .71l-9.5 9.5a.5.5 0 0 1-.22.13l-4 1a.5.5 0 0 1-.61-.61l1-4a.5.5 0 0 1 .13-.22l9.5-9.5ZM12.5 1.21 4.09 9.62l-.62 2.48 2.48-.62 8.41-8.41L12.5 1.21Z"/></svg>
									</a>
									<a href="{{ route('family.create', ['holehouse_id' => $holeHouse->id]) }}" class="btn btn-sm btn-outline-success" title="{{ __('Add member') }}" aria-label="{{ __('Add member') }}">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm0-1a2 2 0 1 1 0-4 2 2 0 0 1 0 4Zm-5.5 7a5.5 5.5 0 0 1 11 0 .5.5 0 0 1-1 0 4.5 4.5 0 0 0-9 0 .5.5 0 0 1-1 0Z"/><path d="M14 2.5a.5.5 0 0 1 .5.5v1.5H16a.5.5 0 0 1 0 1h-1.5V7a.5.5 0 0 1-1 0V5.5H12a.5.5 0 0 1 0-1h1.5V3a.5.5 0 0 1 .5-.5Z"/></svg>
									</a>
									<form method="POST" action="{{ route('hole_houses.destroy', $holeHouse->id) }}" class="d-inline" onsubmit="return confirm(@js(__('Delete this Hole House?')));">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M5.5 5.5a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9.5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1 0-2H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1ZM4 4v9.5a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4H4Zm2.5-2a.5.5 0 0 0-.5.5V3h4v-.5a.5.5 0 0 0-.5-.5h-3Z"/></svg>
										</button>
									</form>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

		@if (method_exists($holeHouses, 'links'))
			<div class="mt-3">{{ $holeHouses->links() }}</div>
		@endif
	@else
		<div class="alert alert-info">
			{{ $search !== '' ? __('No hole houses match your search.') : __('No hole houses found.') }}
		</div>
	@endif
</div>
@endsection
