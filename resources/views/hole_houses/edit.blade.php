@extends('layouts.app')

@section('title', __('Edit Hole House'))

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ __('Edit Hole House') }}</h1>
        <a href="{{ route('hole_houses.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hole_houses.update', $holeHouse->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="census_id" class="form-label">{{ __('Census') }}</label>
            <select id="census_id" name="census_id" class="form-select @error('census_id') is-invalid @enderror" required>
                <option value="">{{ __('Select census') }}</option>
                @foreach ($censuses as $census)
                    <option value="{{ $census->id }}" data-commune-id="{{ $census->commune_id }}" @selected(old('census_id', $holeHouse->census_id) == $census->id)>
                        {{ $census->year }}{{ $census->year_description ? ' - ' . $census->year_description : '' }}{{ $census->commune_name ? ' — ' . $census->commune_name : '' }}
                    </option>
                @endforeach
            </select>
            @error('census_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="hole_number" class="form-label">{{ __('House number') }}</label>
            <input id="hole_number" name="hole_number" type="number" min="1" value="{{ old('hole_number', $holeHouse->hole_number) }}" class="form-control @error('hole_number') is-invalid @enderror" required>
            @error('hole_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="leader_family_name" class="form-label">{{ __('Household leader') }}</label>
            <input id="leader_family_name" name="leader_family_name" type="text" value="{{ old('leader_family_name', $holeHouse->leader_family_name) }}" class="form-control @error('leader_family_name') is-invalid @enderror" required>
            @error('leader_family_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="phone_number" class="form-label">{{ __('Phone number') }}</label>
            <input id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number', $holeHouse->phone_number) }}" class="form-control @error('phone_number') is-invalid @enderror" required>
            @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="village_id" class="form-label">{{ __('Village') }} <span class="text-danger">*</span></label>
            <select id="village_id" name="village_id" class="form-select @error('village_id') is-invalid @enderror" required data-placeholder="{{ __('Select a village') }}">
                <option value="">{{ __('Select a village') }}</option>
                @foreach ($villages as $village)
                    <option value="{{ $village->id }}" data-commune-id="{{ $village->commune_id }}" @selected(old('village_id', $holeHouse->village_id) == $village->id)>{{ $village->name }}</option>
                @endforeach
            </select>
            @error('village_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">{{ __('Additional address details') }}</label>
            <textarea id="address" name="address" rows="2" maxlength="2000" class="form-control @error('address') is-invalid @enderror">{{ old('address', $holeHouse->address) }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">{{ __('Status') }}</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                @foreach (['Occupied', 'Vacant', 'Under construction'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $holeHouse->status) === $status)>{{ __($status) }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Update Hole House') }}</button>
        <a href="{{ route('hole_houses.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const censusSelect = document.getElementById('census_id');
        const villageSelect = document.getElementById('village_id');
        const selectedVillageId = @json(old('village_id', $holeHouse->village_id));
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
