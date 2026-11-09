@extends('layouts.app')

@section('content')
<div class="container py-4">
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">{{ __('Update School') }}</div>
				<div class="card-body">
					<form method="POST" action="{{ route('schools.update', $school) }}">
						@csrf
						@method('PUT')

						<div class="mb-3">
							<label for="name" class="form-label">{{ __('Name') }}</label>
							<input id="name" name="name" type="text" value="{{ old('name', $school->name) }}" class="form-control @error('name') is-invalid @enderror" required>
							@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>

						<div class="row g-3 mb-3">
							<div class="col-md-6">
								<label for="province_id" class="form-label">{{ __('Province') }}</label>
								<select id="province_id" name="province_id" class="form-select" required><option value="">{{ __('Select a province') }}</option></select>
							</div>
							<div class="col-md-6">
								<label for="district_id" class="form-label">{{ __('District') }}</label>
								<select id="district_id" name="district_id" class="form-select" required disabled><option value="">{{ __('Select a district') }}</option></select>
							</div>
							<div class="col-md-6">
								<label for="commune_id" class="form-label">{{ __('Commune') }}</label>
								<select id="commune_id" name="commune_id" class="form-select" required disabled><option value="">{{ __('Select a commune') }}</option></select>
							</div>
							<div class="col-12">
								<fieldset id="village_fieldset" class="border rounded p-3 @error('village_ids') border-danger @enderror" disabled>
									<legend class="float-none w-auto px-2 fs-6">{{ __('Villages') }}</legend>
									<div id="village_options" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2"></div>
								</fieldset>
								<div class="form-text">{{ __('Select one or more villages in the selected commune.') }}</div>
								@error('village_ids')<div class="text-danger small">{{ $message }}</div>@enderror
								@error('village_ids.*')<div class="text-danger small">{{ $message }}</div>@enderror
							</div>
						</div>

						@foreach (['phone' => 'tel', 'email' => 'email', 'director_name' => 'text'] as $field => $inputType)
							<div class="mb-3">
								<label for="{{ $field }}" class="form-label">{{ __(\Illuminate\Support\Str::headline($field)) }}</label>
								<input id="{{ $field }}" name="{{ $field }}" type="{{ $inputType }}" value="{{ old($field, $school->{$field}) }}" class="form-control @error($field) is-invalid @enderror">
								@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						@endforeach

						<div class="mb-3">
							<label for="group_school_id" class="form-label">{{ __('Group School') }}</label>
							<select id="group_school_id" name="group_school_id" class="form-select @error('group_school_id') is-invalid @enderror" required>
								@foreach ($groupSchools as $groupSchool)
									<option value="{{ $groupSchool->id }}" @selected(old('group_school_id', $school->group_school_id) == $groupSchool->id)>{{ $groupSchool->name }}</option>
								@endforeach
							</select>
							@error('group_school_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>

						<button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
						<a href="{{ route('schools.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
					</form>

					<script>
						const locations = @json($locations);
						const selectedLocation = @json($selectedLocation);
						const provinceSelect = document.getElementById('province_id');
						const districtSelect = document.getElementById('district_id');
						const communeSelect = document.getElementById('commune_id');
						const villageFieldset = document.getElementById('village_fieldset');
						const villageOptions = document.getElementById('village_options');
						const oldSelection = {
							province: @json(old('province_id', $selectedLocation['province'] ?? null)),
							district: @json(old('district_id', $selectedLocation['district'] ?? null)),
							commune: @json(old('commune_id', $selectedLocation['commune'] ?? null)),
							villages: @json(old('village_ids', $selectedVillageIds)),
						};

						function populate(select, items, placeholder, selectedValue = '') {
							select.replaceChildren(new Option(placeholder, ''));
							items.forEach((item) => select.add(new Option(item.name, item.id)));
							select.disabled = items.length === 0;
							select.value = items.some((item) => String(item.id) === String(selectedValue)) ? selectedValue : '';
						}
						const getDistricts = () => locations.find((item) => String(item.id) === provinceSelect.value)?.districts ?? [];
						const getCommunes = () => getDistricts().find((item) => String(item.id) === districtSelect.value)?.communes ?? [];
						const getVillages = () => getCommunes().find((item) => String(item.id) === communeSelect.value)?.villages ?? [];

						populate(provinceSelect, locations, @json(__('Select a province')), oldSelection.province);
						populate(districtSelect, getDistricts(), @json(__('Select a district')), oldSelection.district);
						populate(communeSelect, getCommunes(), @json(__('Select a commune')), oldSelection.commune);
						function populateVillages(selectedValues = []) {
							const villages = getVillages();
							const selected = new Set((Array.isArray(selectedValues) ? selectedValues : []).map(String));
							villageOptions.replaceChildren();

							villages.forEach((village) => {
								const wrapper = document.createElement('div');
								wrapper.className = 'col';

								const check = document.createElement('div');
								check.className = 'form-check';

								const input = document.createElement('input');
								input.type = 'checkbox';
								input.className = 'form-check-input';
								input.name = 'village_ids[]';
								input.id = `village-${village.id}`;
								input.value = village.id;
								input.checked = selected.has(String(village.id));

								const label = document.createElement('label');
								label.className = 'form-check-label';
								label.htmlFor = input.id;
								label.textContent = village.name;

								check.append(input, label);
								wrapper.append(check);
								villageOptions.append(wrapper);
							});

							villageFieldset.disabled = villages.length === 0;
						}

						populateVillages(oldSelection.villages);

						provinceSelect.addEventListener('change', () => {
							populate(districtSelect, getDistricts(), @json(__('Select a district')));
							populate(communeSelect, [], @json(__('Select a commune')));
							populateVillages();
						});
						districtSelect.addEventListener('change', () => {
							populate(communeSelect, getCommunes(), @json(__('Select a commune')));
							populateVillages();
						});
						communeSelect.addEventListener('change', () => populateVillages());
					</script>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
