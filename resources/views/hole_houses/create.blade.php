@extends('layouts.app')

@section('content')
<style>
	.house-form { max-width: 960px; margin: 2rem auto; }
	.house-form .card { border: 0; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
	.house-form .card-header { background: #fff; padding: 1.5rem; border-bottom: 1px solid #eee; }
	.house-form .card-body { padding: 1.5rem; }
	.house-form .section-title { color: #495057; font-size: 1rem; font-weight: 600; margin: 1.25rem 0 1rem; }
	.house-form .form-label { font-weight: 500; }
</style>

<div class="container house-form">
	<div class="card">
		<div class="card-header">
			<h1 class="h4 mb-1">{{ __('Register a Hole House') }}</h1>
			<p class="text-muted mb-0">{{ __('Enter the house and household details below.') }}</p>
		</div>
		<div class="card-body">
			@if ($errors->any())
				<div class="alert alert-danger" role="alert">
					<strong>{{ __('Please correct the following:') }}</strong>
					<ul class="mb-0 mt-2">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			<form method="POST" action="{{ route('hole_houses.store') }}">
				@csrf

				<h2 class="section-title">{{ __('House details') }}</h2>
				<div class="row">
					<div class="col-md-6 mb-3">
						<label for="census_id" class="form-label">{{ __('Census') }} <span class="text-danger">*</span></label>
						<select class="form-select @error('census_id') is-invalid @enderror" id="census_id" name="census_id" required>
							<option value="">{{ __('Select census') }}</option>
							@foreach ($censuses as $census)
								<option value="{{ $census->id }}" data-commune-id="{{ $census->commune_id }}" @selected(old('census_id') == $census->id)>
									{{ $census->year }}{{ $census->year_description ? ' - ' . $census->year_description : '' }}{{ $census->commune_name ? ' — ' . $census->commune_name : '' }}
								</option>
							@endforeach
						</select>
						@error('census_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-6 mb-3">
						<label for="hole_number" class="form-label">{{ __('House number') }} <span class="text-danger">*</span></label>
						<input type="number" min="1" class="form-control @error('hole_number') is-invalid @enderror" id="hole_number" name="hole_number" value="{{ old('hole_number') }}" required>
						@error('hole_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-6 mb-3">
						<label for="status" class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
						<select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
							<option value="">{{ __('Select status') }}</option>
							@foreach (['Occupied', 'Vacant', 'Under construction'] as $status)
								<option value="{{ $status }}" @selected(old('status') === $status)>{{ __($status) }}</option>
							@endforeach
						</select>
						@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>

				<h2 class="section-title">{{ __('Household contact') }}</h2>
				<div class="row">
					<div class="col-md-6 mb-3">
						<label for="leader_family_name" class="form-label">{{ __('Household leader') }} <span class="text-danger">*</span></label>
						<input type="text" class="form-control @error('leader_family_name') is-invalid @enderror" id="leader_family_name" name="leader_family_name" value="{{ old('leader_family_name') }}" required>
						@error('leader_family_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-md-6 mb-3">
						<label for="phone_number" class="form-label">{{ __('Phone number') }} <span class="text-danger">*</span></label>
						<input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
						@error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-12 mb-3">
						<label for="village_id" class="form-label">{{ __('Village') }} <span class="text-danger">*</span></label>
						<select id="village_id" name="village_id" class="form-select @error('village_id') is-invalid @enderror" required data-placeholder="{{ __('Select a village') }}">
							<option value="">{{ __('Select a village') }}</option>
							@foreach ($villages as $village)
								<option value="{{ $village->id }}" data-commune-id="{{ $village->commune_id }}" @selected(old('village_id') == $village->id)>{{ $village->name }}</option>
							@endforeach
						</select>
						@error('village_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
					<div class="col-12 mb-3">
						<label for="address" class="form-label">{{ __('Additional address details') }}</label>
						<textarea id="address" name="address" rows="2" maxlength="2000" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
						@error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
					</div>
				</div>

				<div class="d-flex justify-content-end gap-2 mt-4">
					<a href="{{ route('hole_houses.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
					<button type="submit" class="btn btn-primary">{{ __('Save House') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		const censusSelect = document.getElementById('census_id');
		const villageSelect = document.getElementById('village_id');
		const selectedVillageId = @json(old('village_id'));
		const placeholder = villageSelect.dataset.placeholder;
		const villages = @json($villages);

		function updateVillages() {
			const communeId = censusSelect.selectedOptions[0]?.dataset.communeId;
			const currentVillageId = villageSelect.value || selectedVillageId;

			villageSelect.replaceChildren(new Option(placeholder, ''));
			villages.forEach(function (village) {
				if (String(village.commune_id) === String(communeId)) {
					villageSelect.add(new Option(village.name, village.id, false, String(village.id) === String(currentVillageId)));
				}
			});
			villageSelect.disabled = !communeId || villageSelect.options.length === 1;
		}

		censusSelect.addEventListener('change', updateVillages);
		updateVillages();
	});
</script>
@endsection
