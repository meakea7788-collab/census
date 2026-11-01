@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="mb-4">
                        <h1 class="h3 mb-2">Edit Group School</h1>
                        <p class="text-secondary mb-0">Update the selected group school details.</p>
                    </div>

                    <form action="{{ route('group-school.update', $groupSchool) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Group School Name</label>
                            <input type="text" id="name" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $groupSchool->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="director_name" class="form-label">Director Name</label>
                            <input type="text" id="director_name" name="director_name"
                                   class="form-control @error('director_name') is-invalid @enderror"
                                   value="{{ old('director_name', $groupSchool->director_name) }}" required>
                            @error('director_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" id="phone" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $groupSchool->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="province_id" class="form-label">Province</label>
                            <select id="province_id" name="province_id" class="form-select @error('province_id') is-invalid @enderror" required>
                                <option value="">Select a province</option>
                            </select>
                            @error('province_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <label for="district_id" class="form-label mt-3">District</label>
                            <select id="district_id" name="district_id" class="form-select @error('district_id') is-invalid @enderror" required disabled>
                                <option value="">Select a district</option>
                            </select>
                            @error('district_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <label for="commune_id" class="form-label mt-3">Commune</label>
                            <select id="commune_id" name="commune_id" class="form-select @error('commune_id') is-invalid @enderror" required disabled>
                                <option value="">Select a commune</option>
                            </select>
                            @error('commune_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <label for="village_id" class="form-label mt-3">Village</label>
                            <select id="village_id" name="village_id" class="form-select @error('village_id') is-invalid @enderror" required disabled>
                                <option value="">Select a village</option>
                            </select>
                            @error('village_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                            <a href="{{ route('group-school.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const locations = @json($locations);
    const savedLocation = @json($selectedLocation);
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

    function getDistricts() {
        return locations.find((province) => String(province.id) === provinceSelect.value)?.districts ?? [];
    }

    function getCommunes() {
        return getDistricts().find((district) => String(district.id) === districtSelect.value)?.communes ?? [];
    }

    function getVillages() {
        return getCommunes().find((commune) => String(commune.id) === communeSelect.value)?.villages ?? [];
    }

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

    communeSelect.addEventListener('change', () => {
        populate(villageSelect, getVillages(), 'Select a village');
    });
</script>
@endsection
