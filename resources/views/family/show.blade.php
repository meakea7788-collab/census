@extends('layouts.app')

@section('title', __('Family Member Details'))

@section('content')
<div class="container py-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h1 class="h3 mb-0">{{ __('Family Member Details') }}</h1>
		<div class="d-flex gap-2">
			<a href="{{ route('family.edit', $family->id) }}" class="btn btn-outline-warning">{{ __('Edit') }}</a>
			@if (! $family->is_deceased)
				<a href="{{ route('family.move.edit', $family->id) }}" class="btn btn-outline-info" title="{{ __('Record move address') }}">{{ __('Move') }}</a>
				<form method="POST" action="{{ route('family.mark-deceased', $family->id) }}" class="d-inline" onsubmit="return confirm(@js(__('Mark this family member as deceased?')));">
					@csrf
					<button type="submit" class="btn btn-outline-secondary" title="{{ __('Mark deceased') }}">{{ __('Mark deceased') }}</button>
				</form>
			@endif
			<a href="{{ $family->holehouse_id ? route('hole_houses.show', $family->holehouse_id) : route('family.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
		</div>
	</div>

	@if (session('success'))
		<div class="alert alert-success" role="alert">{{ session('success') }}</div>
	@endif
	@if (session('error'))
		<div class="alert alert-danger" role="alert">{{ session('error') }}</div>
	@endif

	<div class="card border-0 shadow-sm">
		<div class="card-body">
			<dl class="row mb-0">
				<dt class="col-sm-3">{{ __('House') }}</dt>
				<dd class="col-sm-9">{{ $family->holehouse?->hole_number ?? '—' }}</dd>

				<dt class="col-sm-3">{{ __('Household leader') }}</dt>
				<dd class="col-sm-9">{{ $family->holehouse?->leader_family_name ?? '—' }}</dd>

				<dt class="col-sm-3">{{ __('Family name') }}</dt>
				<dd class="col-sm-9">{{ $family->family_name }}</dd>

				<dt class="col-sm-3">{{ __('Gender') }}</dt>
				<dd class="col-sm-9">{{ $family->gender }}</dd>

				<dt class="col-sm-3">{{ __('Date of birth') }}</dt>
				<dd class="col-sm-9">{{ $family->date_of_birth }}</dd>

				<dt class="col-sm-3">{{ __('Relationship') }}</dt>
				<dd class="col-sm-9">{{ $family->relationship }}</dd>

				<dt class="col-sm-3">{{ __('Job') }}</dt>
				<dd class="col-sm-9">{{ $family->job }}</dd>

				<dt class="col-sm-3">{{ __('Moved to address') }}</dt>
				<dd class="col-sm-9">{{ $family->moved_to_address ?? '—' }}</dd>

				<dt class="col-sm-3">{{ __('Status') }}</dt>
				<dd class="col-sm-9">{{ $family->is_deceased ? __('Deceased') : __('Active') }}</dd>
			</dl>
		</div>
	</div>

	<div class="mt-4">
		<h2 class="h4 mb-3">{{ __('Other members in this household') }}</h2>
		<div class="card border-0 shadow-sm">
			<div class="table-responsive">
				<table class="table table-striped mb-0">
					<thead>
						<tr>
							<th scope="col">#</th>
							<th scope="col">{{ __('Family name') }}</th>
							<th scope="col">{{ __('Relationship') }}</th>
							<th scope="col">{{ __('Gender') }}</th>
							<th scope="col">{{ __('Date of birth') }}</th>
							<th scope="col">{{ __('Job') }}</th>
							<th scope="col">{{ __('Status') }}</th>
						</tr>
					</thead>
					<tbody>
						@forelse ($family->holehouse?->families ?? collect() as $member)
							<tr @class(['table-primary' => $member->is($family)])>
								<td>{{ $member->id }}</td>
								<td>
									{{ $member->family_name }}
									@if ($member->is($family))
										<span class="badge text-bg-primary">{{ __('Current member') }}</span>
									@endif
								</td>
								<td>{{ $member->relationship }}</td>
								<td>{{ $member->gender }}</td>
								<td>{{ $member->date_of_birth }}</td>
								<td>{{ $member->job }}</td>
								<td>{{ $member->is_deceased ? __('Deceased') : __('Active') }}</td>
							</tr>
						@empty
							<tr>
								<td colspan="7" class="text-center py-4">{{ __('No members are registered for this household.') }}</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
