@extends('layouts.app')

@section('content')
<div class="container py-4">
	<h1 class="mb-4">{{ __('Create School') }}</h1>

	<form method="POST" action="{{ route('schools.store') }}">
		@csrf

		<div class="row g-3">
			<div class="col-12">
				<label for="name" class="form-label">{{ __('School name') }}</label>
				<input type="text" name="name" id="name" value="{{ old('name') }}"
					   class="form-control @error('name') is-invalid @enderror" required>
				@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-sm-6 col-xl-3">
				<label for="province_id" class="form-label">{{ __('Province') }}</label>
				<select id="province_id" name="province_id" class="form-select @error('province_id') is-invalid @enderror" required>
					<option value="">{{ __('Select a province') }}</option>
				</select>
				@error('province_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-sm-6 col-xl-3">
				<label for="district_id" class="form-label">{{ __('District') }}</label>
				<select id="district_id" name="district_id" class="form-select @error('district_id') is-invalid @enderror" required disabled>
					<option value="">{{ __('Select a district') }}</option>
				</select>
				@error('district_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-sm-6 col-xl-3">
				<label for="commune_id" class="form-label">{{ __('Commune') }}</label>
				<select id="commune_id" name="commune_id" class="form-select @error('commune_id') is-invalid @enderror" required disabled>
					<option value="">{{ __('Select a commune') }}</option>
				</select>
				@error('commune_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

			<div class="col-12">
				<label for="address" class="form-label">{{ __('Address') }}</label>
				<textarea name="address" id="address" rows="3"
						  class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
				@error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-md-4">
				<label for="phone" class="form-label">{{ __('Phone') }}</label>
				<input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
					   class="form-control @error('phone') is-invalid @enderror">
				@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-md-4">
				<label for="email" class="form-label">{{ __('Email') }}</label>
				<input type="email" name="email" id="email" value="{{ old('email') }}"
					   class="form-control @error('email') is-invalid @enderror">
				@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-md-4">
				<label for="director_name" class="form-label">{{ __('Director') }}</label>
				<input type="text" name="director_name" id="director_name" value="{{ old('director_name') }}"
					   class="form-control @error('director_name') is-invalid @enderror">
				@error('director_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12">
				<label for="group_school_id" class="form-label">{{ __('Group School') }}</label>
				<select name="group_school_id" id="group_school_id"
						class="form-select @error('group_school_id') is-invalid @enderror" required disabled>
					<option value="">{{ __('Select a group school') }}</option>
				</select>
				@error('group_school_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>
		</div>

		<div class="mt-3">
			<button type="submit" class="btn btn-primary">{{ __('Create School') }}</button>
			<a href="{{ route('schools.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
		</div>
	</form>
</div>

<script>
    const locations = @json($locations);
    const groupSchools = @json($groupSchoolOptions);

    const provinceSelect = document.getElementById('province_id');
    const districtSelect = document.getElementById('district_id');
    const communeSelect = document.getElementById('commune_id');
    const villageFieldset = document.getElementById('village_fieldset');
    const villageOptions = document.getElementById('village_options');
    const groupSchoolSelect = document.getElementById('group_school_id');

    const oldSelection = {
        province: @json(old('province_id', $selectedLocation['province'] ?? null)),
        district: @json(old('district_id', $selectedLocation['district'] ?? null)),
        commune: @json(old('commune_id', $selectedLocation['commune'] ?? null)),
        villages: @json(old('village_ids', [])),
        groupSchool: @json(old('group_school_id', $selectedGroupSchool?->id)),
    };

    function populate(select, items, placeholder, selectedValue = '') {
        select.replaceChildren(new Option(placeholder, ''));
        items.forEach((item) => select.add(new Option(item.name, item.id)));
        select.disabled = items.length === 0;
        select.value = items.some((item) => String(item.id) === String(selectedValue)) ? selectedValue : '';
    }

    function getDistricts() {
        return locations.find((province) => String(province.id) === provinceSelect.value)?.districts ?? [];
    }

    function getCommunes() {
        return getDistricts().find((district) => String(district.id) === districtSelect.value)?.communes ?? [];
    }

    function getVillages() {
        return getCommunes().find((commune) => String(commune.id) === communeSelect.value)?.villages ?? [];
    }

    function updateGroupSchools() {
        const commune = getCommunes().find((item) => String(item.id) === communeSelect.value);
        const communeName = (commune?.name ?? '').toLowerCase();
        const filtered = groupSchools.filter((groupSchool) => {
            const address = (groupSchool.address ?? '').toLowerCase();
            return communeName && address.includes(communeName);
        });

        groupSchoolSelect.replaceChildren(new Option('Select a group school', ''));

        if (filtered.length === 0) {
            groupSchoolSelect.disabled = true;
            groupSchoolSelect.add(new Option('No group schools in this commune', ''));
            return;
        }

        filtered.forEach((groupSchool) => {
            groupSchoolSelect.add(new Option(groupSchool.name, groupSchool.id));
        });

        groupSchoolSelect.disabled = false;

        if (filtered.some((groupSchool) => String(groupSchool.id) === String(oldSelection.groupSchool))) {
            groupSchoolSelect.value = String(oldSelection.groupSchool);
        }
    }

    populate(provinceSelect, locations, 'Select a province', oldSelection.province);
    populate(districtSelect, getDistricts(), 'Select a district', oldSelection.district);
    populate(communeSelect, getCommunes(), 'Select a commune', oldSelection.commune);
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
    updateGroupSchools();

    provinceSelect.addEventListener('change', () => {
        populate(districtSelect, getDistricts(), 'Select a district');
        populate(communeSelect, [], 'Select a commune');
        populateVillages();
        updateGroupSchools();
    });

    districtSelect.addEventListener('change', () => {
        populate(communeSelect, getCommunes(), 'Select a commune');
        populateVillages();
        updateGroupSchools();
    });

    communeSelect.addEventListener('change', () => {
        populateVillages();
        updateGroupSchools();
    });

    villageOptions.addEventListener('change', () => {
        updateGroupSchools();
    });
</script>
@endsection
