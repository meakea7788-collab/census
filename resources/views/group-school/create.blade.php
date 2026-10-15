@extends('layouts.app')

@section('title', 'Create Group School')

@section('content')
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="mb-4">
                            <h1 class="h3 mb-2">Create Group School</h1>
                            <p class="text-secondary mb-0">Enter the group school and director details below.</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success" role="status">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('group-school.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Group School Name</label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    maxlength="255"
                                    required
                                    autofocus
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="director_name" class="form-label">Director Name</label>
                                <input
                                    type="text"
                                    id="director_name"
                                    name="director_name"
                                    value="{{ old('director_name') }}"
                                    class="form-control @error('director_name') is-invalid @enderror"
                                    maxlength="255"
                                    required
                                >
                                @error('director_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    maxlength="50"
                                    required
                                >
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

                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <button type="submit" class="btn btn-primary px-4">Create Group School</button>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const locations = @json($locations);

        const provinceSelect = document.getElementById('province_id');
        const districtSelect = document.getElementById('district_id');
        const communeSelect = document.getElementById('commune_id');
        const villageSelect = document.getElementById('village_id');
        const oldSelection = {
            province: @json(old('province_id')),
            district: @json(old('district_id')),
            commune: @json(old('commune_id')),
            village: @json(old('village_id')),
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
