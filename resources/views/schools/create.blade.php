@extends('layouts.app')

@section('content')
<div class="container py-4">
	<h1 class="mb-4">Create School</h1>

	<form method="POST" action="{{ route('schools.store') }}">
		@csrf

		<div class="row g-3">
			<div class="col-12">
				<label for="name" class="form-label">School name</label>
				<input type="text" name="name" id="name" value="{{ old('name') }}"
					   class="form-control @error('name') is-invalid @enderror" required>
				@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-sm-6 col-xl-3">
				<label for="province_id" class="form-label">Province</label>
				<select id="province_id" name="province_id" class="form-select @error('province_id') is-invalid @enderror" required>
					<option value="">Select a province</option>
				</select>
				@error('province_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-sm-6 col-xl-3">
				<label for="district_id" class="form-label">District</label>
				<select id="district_id" name="district_id" class="form-select @error('district_id') is-invalid @enderror" required disabled>
					<option value="">Select a district</option>
				</select>
				@error('district_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-sm-6 col-xl-3">
				<label for="commune_id" class="form-label">Commune</label>
				<select id="commune_id" name="commune_id" class="form-select @error('commune_id') is-invalid @enderror" required disabled>
					<option value="">Select a commune</option>
				</select>
				@error('commune_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-sm-6 col-xl-3">
				<label for="village_id" class="form-label">Village</label>
				<select id="village_id" name="village_id" class="form-select @error('village_id') is-invalid @enderror" required disabled>
					<option value="">Select a village</option>
				</select>
				@error('village_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12">
				<label for="address" class="form-label">Address</label>
				<textarea name="address" id="address" rows="3"
						  class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
				@error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-md-4">
				<label for="phone" class="form-label">Phone</label>
				<input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
					   class="form-control @error('phone') is-invalid @enderror">
				@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-md-4">
				<label for="email" class="form-label">Email</label>
				<input type="email" name="email" id="email" value="{{ old('email') }}"
					   class="form-control @error('email') is-invalid @enderror">
				@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12 col-md-4">
				<label for="director_name" class="form-label">Director</label>
				<input type="text" name="director_name" id="director_name" value="{{ old('director_name') }}"
					   class="form-control @error('director_name') is-invalid @enderror">
				@error('director_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="col-12">
				<label for="group_school_id" class="form-label">Group School</label>
				<select name="group_school_id" id="group_school_id"
						class="form-select @error('group_school_id') is-invalid @enderror" required disabled>
					<option value="">Select a group school</option>
				</select>
				@error('group_school_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>
		</div>

		<div class="mt-3">
			<button type="submit" class="btn btn-primary">Create School</button>
			<a href="{{ route('schools.index') }}" class="btn btn-secondary">Cancel</a>
		</div>
	</form>
</div>

<script>
    const locations = @json($locations);
    const groupSchools = @json($groupSchoolOptions);

    const provinceSelect = document.getElementById('province_id');
    const districtSelect = document.getElementById('district_id');
    const communeSelect = document.getElementById('commune_id');
    const villageSelect = document.getElementById('village_id');
    const groupSchoolSelect = document.getElementById('group_school_id');

    const oldSelection = {
        province: @json(old('province_id')),
        district: @json(old('district_id')),
        commune: @json(old('commune_id')),
        village: @json(old('village_id')),
        groupSchool: @json(old('group_school_id')),
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
    populate(villageSelect, getVillages(), 'Select a village', oldSelection.village);
    updateGroupSchools();

    provinceSelect.addEventListener('change', () => {
        populate(districtSelect, getDistricts(), 'Select a district');
        populate(communeSelect, [], 'Select a commune');
        populate(villageSelect, [], 'Select a village');
        updateGroupSchools();
    });

    districtSelect.addEventListener('change', () => {
        populate(communeSelect, getCommunes(), 'Select a commune');
        populate(villageSelect, [], 'Select a village');
        updateGroupSchools();
    });

    communeSelect.addEventListener('change', () => {
        populate(villageSelect, getVillages(), 'Select a village');
        updateGroupSchools();
    });

    villageSelect.addEventListener('change', () => {
        updateGroupSchools();
    });
</script>
@endsection
