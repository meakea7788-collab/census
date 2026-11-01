@extends('layouts.app')

@section('content')
<div class="container py-4">
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">Update School</div>
				<div class="card-body">
					<form method="POST" action="{{ route('schools.update', $school) }}">
						@csrf
						@method('PUT')

						<div class="mb-3">
							<label for="name" class="form-label">Name</label>
							<input id="name" name="name" type="text" value="{{ old('name', $school->name) }}" class="form-control @error('name') is-invalid @enderror" required>
							@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>

						<div class="row g-3 mb-3">
							<div class="col-md-6">
								<label for="province_id" class="form-label">Province</label>
								<select id="province_id" name="province_id" class="form-select" required><option value="">Select a province</option></select>
							</div>
							<div class="col-md-6">
								<label for="district_id" class="form-label">District</label>
								<select id="district_id" name="district_id" class="form-select" required disabled><option value="">Select a district</option></select>
							</div>
							<div class="col-md-6">
								<label for="commune_id" class="form-label">Commune</label>
								<select id="commune_id" name="commune_id" class="form-select" required disabled><option value="">Select a commune</option></select>
							</div>
							<div class="col-md-6">
								<label for="village_id" class="form-label">Village</label>
								<select id="village_id" name="village_id" class="form-select" required disabled><option value="">Select a village</option></select>
							</div>
						</div>

						@foreach (['phone' => 'tel', 'email' => 'email', 'director_name' => 'text'] as $field => $inputType)
							<div class="mb-3">
								<label for="{{ $field }}" class="form-label">{{ \Illuminate\Support\Str::headline($field) }}</label>
								<input id="{{ $field }}" name="{{ $field }}" type="{{ $inputType }}" value="{{ old($field, $school->{$field}) }}" class="form-control @error($field) is-invalid @enderror">
								@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						@endforeach

						<div class="mb-3">
							<label for="group_school_id" class="form-label">Group School</label>
							<select id="group_school_id" name="group_school_id" class="form-select @error('group_school_id') is-invalid @enderror" required>
								@foreach ($groupSchools as $groupSchool)
									<option value="{{ $groupSchool->id }}" @selected(old('group_school_id', $school->group_school_id) == $groupSchool->id)>{{ $groupSchool->name }}</option>
								@endforeach
							</select>
							@error('group_school_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>

						<button type="submit" class="btn btn-primary">Save changes</button>
						<a href="{{ route('schools.index') }}" class="btn btn-secondary">Cancel</a>
					</form>

					<script>
						const locations = @json($locations);
						const selectedLocation = @json($selectedLocation);
						const provinceSelect = document.getElementById('province_id');
						const districtSelect = document.getElementById('district_id');
						const communeSelect = document.getElementById('commune_id');
						const villageSelect = document.getElementById('village_id');
						const oldSelection = {
							province: @json(old('province_id', $selectedLocation['province'] ?? null)),
							district: @json(old('district_id', $selectedLocation['district'] ?? null)),
							commune: @json(old('commune_id', $selectedLocation['commune'] ?? null)),
							village: @json(old('village_id', $selectedLocation['village'] ?? null)),
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

						populate(provinceSelect, locations, 'Select a province', oldSelection.province);
						populate(districtSelect, getDistricts(), 'Select a district', oldSelection.district);
						populate(communeSelect, getCommunes(), 'Select a commune', oldSelection.commune);
						populate(villageSelect, getVillages(), 'Select a village', oldSelection.village);

						provinceSelect.addEventListener('change', () => {
							populate(districtSelect, getDistricts(), 'Select a district');
							populate(communeSelect, [], 'Select a commune');
							populate(villageSelect, [], 'Select a village');
						});
						districtSelect.addEventListener('change', () => {
							populate(communeSelect, getCommunes(), 'Select a commune');
							populate(villageSelect, [], 'Select a village');
						});
						communeSelect.addEventListener('change', () => populate(villageSelect, getVillages(), 'Select a village'));
					</script>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
