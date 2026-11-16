@extends('layouts.app')

@section('content')
<style>
	.hole-house-card {
		border: 1.5px solid #2b2b2b;
		border-radius: 18px;
		background: #fff;
		transition: transform 0.15s ease, box-shadow 0.15s ease;
	}
	.hole-house-card:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
	}
	.hole-house-card-header {
		border-bottom: 1.5px solid #555;
		padding: 0.6rem 1rem;
		text-align: center;
		font-weight: 700;
		font-size: 1.25rem;
		color: #000;
	}
	.hole-house-card-body {
		padding: 0.85rem 1rem 0.5rem;
		min-height: 155px;
		font-size: 0.95rem;
	}
	.hole-house-member-row {
		display: flex;
		align-items: baseline;
		gap: 0.4rem;
		margin-bottom: 0.35rem;
		line-height: 1.4;
	}
	.hole-house-member-index {
		min-width: 1.25rem;
		color: #111;
	}
	.hole-house-member-name {
		color: #111;
	}
	.hole-house-member-relation {
		color: #e02424;
		margin-left: 0.35rem;
	}
	.hole-house-card-footer {
		padding: 0.4rem 1rem 0.75rem;
		text-align: center;
	}
	.hole-house-card-footer a {
		color: #3b5998;
		font-style: italic;
		text-decoration: none;
		font-size: 0.92rem;
	}
	.hole-house-card-footer a:hover {
		text-decoration: underline;
	}
</style>

<div class="container py-4">
	<div class="mb-3">
		@if (isset($years) && $years->isNotEmpty())
			<div class="d-flex flex-wrap align-items-center gap-2 mb-2">
				<form method="GET" action="{{ route('hole_houses.index') }}" class="m-0 d-flex align-items-center gap-2">
					@if (($villageId ?? '') !== '')
						<input type="hidden" name="village_id" value="{{ $villageId }}">
					@endif
					@if ($search !== '')
						<input type="hidden" name="search" value="{{ $search }}">
					@endif
					<label for="census-filter" class="fw-semibold text-secondary mb-0 small">{{ __('Census') }}:</label>
					<select
						id="census-filter"
						name="year_id"
						class="form-select form-select-sm"
						style="min-width: 150px;"
						onchange="this.form.submit()"
					>
						<option value="">{{ __('Select census') }}</option>
						@foreach ($years as $yearOption)
							<option value="{{ $yearOption->id }}" @selected((string) ($yearId ?? '') === (string) $yearOption->id)>
								{{ $yearOption->year }}
							</option>
						@endforeach
					</select>
				</form>
			</div>
		@endif

		<div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
			<div class="d-flex flex-wrap align-items-center gap-2">
				<h1 class="h3 mb-0">{{ __('Hole Houses') }}</h1>
				<form method="GET" action="{{ route('hole_houses.index') }}" class="m-0">
					@if ($search !== '')
						<input type="hidden" name="search" value="{{ $search }}">
					@endif
					@if (($yearId ?? '') !== '')
						<input type="hidden" name="year_id" value="{{ $yearId }}">
					@endif
					<label for="village-filter" class="visually-hidden">{{ __('Select village') }}</label>
					<select
						id="village-filter"
						name="village_id"
						class="form-select"
						style="min-width: 180px;"
						onchange="this.form.submit()"
					>
						<option value="">{{ __('Select village') }}</option>
						@foreach ($villages ?? [] as $village)
							<option value="{{ $village->id }}" @selected((string) ($villageId ?? '') === (string) $village->id)>
								{{ $village->name }}
							</option>
						@endforeach
					</select>
				</form>
				<div class="badge bg-light text-dark border px-3 py-2 fs-6 fw-normal d-flex align-items-center gap-1" style="height: 38px;">
					<span class="text-secondary">{{ __('Amount of Hole Houses') }}:</span>
					<strong class="text-primary fs-6">{{ method_exists($holeHouses, 'total') ? $holeHouses->total() : ($holeHouses?->count() ?? 0) }}</strong>
				</div>
			</div>
			<div class="d-flex flex-wrap align-items-center gap-2">
				<form method="GET" action="{{ route('hole_houses.index') }}" class="d-flex gap-2">
					@if (($yearId ?? '') !== '')
						<input type="hidden" name="year_id" value="{{ $yearId }}">
					@endif
					@if (($villageId ?? '') !== '')
						<input type="hidden" name="village_id" value="{{ $villageId }}">
					@endif
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
					@if ($search !== '' || ($villageId ?? '') !== '' || ($yearId ?? '') !== '')
						<a href="{{ route('hole_houses.index') }}" class="btn btn-outline-secondary">{{ __('Clear') }}</a>
					@endif
				</form>
				<div class="btn-group" role="group" aria-label="{{ __('Choose record view') }}">
					<button class="btn btn-outline-primary active" type="button" data-view="cards">{{ __('Cards') }}</button>
					<button class="btn btn-outline-primary" type="button" data-view="list">{{ __('List') }}</button>
				</div>
				<a href="{{ route('hole_houses.create') }}" class="btn btn-primary">{{ __('Create Hole House') }}</a>
			</div>
		</div>
	</div>

	@if (session('success'))
		<div class="alert alert-success" role="alert">{{ session('success') }}</div>
	@endif

	@if (isset($holeHouses) && $holeHouses->count())
		<div id="cardsView" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
			@foreach ($holeHouses->sortBy('hole_number')->values() as $holeHouse)
				<div class="col">
					<div class="hole-house-card h-100 d-flex flex-column">
						<div class="hole-house-card-header">
							{{ str_pad((string) $holeHouse->hole_number, 3, '0', STR_PAD_LEFT) }}
						</div>
						<div class="hole-house-card-body flex-grow-1">
							@forelse ($holeHouse->families->take(5) as $index => $member)
								<div class="hole-house-member-row">
									<span class="hole-house-member-index">{{ $index + 1 }}.</span>
									<div>
										<span class="hole-house-member-name">{{ $member->family_name }}</span>
										@if ($member->relationship)
											<span class="hole-house-member-relation">{{ $member->relationship }}</span>
										@endif
									</div>
								</div>
							@empty
								@if ($holeHouse->leader_family_name)
									<div class="hole-house-member-row">
										<span class="hole-house-member-index">1.</span>
										<div>
											<span class="hole-house-member-name">{{ $holeHouse->leader_family_name }}</span>
											<span class="hole-house-member-relation">{{ __('Household leader') }}</span>
										</div>
									</div>
								@endif
							@endforelse
						</div>
						<div class="hole-house-card-footer mt-auto">
							<a href="{{ route('hole_houses.show', $holeHouse->id) }}">
								{{ __('View all members') }}
							</a>
						</div>
					</div>
				</div>
			@endforeach
		</div>

		<div id="listView" class="card d-none">
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
			{{ ($search !== '' || ($villageId ?? '') !== '' || ($yearId ?? '') !== '' || ($censusId ?? '') !== '') ? __('No hole houses match your search.') : __('No hole houses found.') }}
		</div>
	@endif
</div>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		const buttons = document.querySelectorAll('[data-view]');
		const cardsView = document.getElementById('cardsView');
		const listView = document.getElementById('listView');
		const storageKey = 'hole_houses_view';

		function setView(view) {
			const showCards = view !== 'list';
			buttons.forEach(item => item.classList.toggle('active', item.dataset.view === (showCards ? 'cards' : 'list')));
			cardsView?.classList.toggle('d-none', !showCards);
			listView?.classList.toggle('d-none', showCards);
			try {
				localStorage.setItem(storageKey, showCards ? 'cards' : 'list');
			} catch (e) {}
		}

		let savedView = 'cards';
		try {
			savedView = localStorage.getItem(storageKey) || 'cards';
		} catch (e) {}
		setView(savedView);

		buttons.forEach(button => {
			button.addEventListener('click', () => setView(button.dataset.view));
		});
	});
</script>
@endsection
