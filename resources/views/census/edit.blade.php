@extends('layouts.app')

@section('title', __('Edit Census'))

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">{{ __('Edit Census') }}</h1>
        <a href="{{ route('census.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('census.update', $census->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label for="year_id" class="form-label">{{ __('Year') }}</label>
                <select id="year_id" name="year_id" class="form-select" required>
                    <option value="">{{ __('Select year') }}</option>
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}" @selected(old('year_id', $census->year_id) == $year->id)>
                            {{ $year->year }}{{ $year->description ? ' - ' . $year->description : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="group_school_id" class="form-label">{{ __('Group School') }}</label>
                <select id="group_school_id" name="group_school_id" class="form-select" required>
                    <option value="">{{ __('Select group school') }}</option>
                    @foreach ($groupSchools as $groupSchool)
                        <option value="{{ $groupSchool->id }}" @selected(old('group_school_id', $census->group_school_id) == $groupSchool->id)>
                            {{ $groupSchool->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="school_id" class="form-label">{{ __('School') }}</label>
                <select id="school_id" name="school_id" class="form-select" required>
                    <option value="">{{ __('Select school') }}</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}" @selected(old('school_id', $census->school_id) == $school->id)>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="province_id" class="form-label">{{ __('Province') }}</label>
                <select id="province_id" name="province_id" class="form-select" required>
                    <option value="">{{ __('Select province') }}</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->id }}" @selected(old('province_id', $census->province_id) == $province->id)>
                            {{ $province->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="district_id" class="form-label">{{ __('District') }}</label>
                <select id="district_id" name="district_id" class="form-select" required>
                    <option value="">{{ __('Select district') }}</option>
                    @foreach ($districts as $district)
                        <option value="{{ $district->id }}" @selected(old('district_id', $census->district_id) == $district->id)>
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="commune_id" class="form-label">{{ __('Commune') }}</label>
                <select id="commune_id" name="commune_id" class="form-select" required>
                    <option value="">{{ __('Select commune') }}</option>
                    @foreach ($communes as $commune)
                        <option value="{{ $commune->id }}" @selected(old('commune_id', $census->commune_id) == $commune->id)>
                            {{ $commune->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="village_id" class="form-label">{{ __('Village') }}</label>
                <select id="village_id" name="village_id" class="form-select" required>
                    <option value="">{{ __('Select village') }}</option>
                    @foreach ($villages as $village)
                        <option value="{{ $village->id }}" @selected(old('village_id', $census->village_id) == $village->id)>
                            {{ $village->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">{{ __('Teachers') }}</label>
                @php($selectedTeacherIds = collect(old('teacher_id', $teacherIds ?? []))->map(fn ($id) => (string) $id)->all())
                @php($selectedTeacherNames = $teachers->whereIn('id', $selectedTeacherIds)->pluck('name')->all())
                <details class="teacher-dropdown">
                    <summary class="form-select teacher-dropdown-label" title="{{ implode(', ', $selectedTeacherNames) }}">
                        {{ $selectedTeacherNames ? implode(', ', $selectedTeacherNames) : __('Select teachers') }}
                    </summary>
                    <div class="teacher-dropdown-menu">
                        @foreach ($teachers as $teacher)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="teacher_id[]" value="{{ $teacher->id }}" id="teacher_{{ $teacher->id }}" @checked(in_array((string) $teacher->id, $selectedTeacherIds, true))>
                                <label class="form-check-label" for="teacher_{{ $teacher->id }}">{{ $teacher->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </details>
                @error('teacher_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                @error('teacher_id.*')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label for="note" class="form-label">{{ __('Note') }}</label>
                <textarea id="note" name="note" rows="4" class="form-control">{{ old('note', $census->note) }}</textarea>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ __('Update Census') }}</button>
            <a href="{{ route('census.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
<script>
    const locationTree = @json($locationTree);
    const schoolOptions = @json($schoolOptions);
    const schoolSelect = document.getElementById('school_id');
    const groupSchoolSelect = document.getElementById('group_school_id');
    const provinceSelect = document.getElementById('province_id');
    const districtSelect = document.getElementById('district_id');
    const communeSelect = document.getElementById('commune_id');
    const villageSelect = document.getElementById('village_id');

    schoolSelect.addEventListener('change', () => {
        const school = schoolOptions.find(item => String(item.id) === String(schoolSelect.value));
        if (!school) return;

        groupSchoolSelect.value = String(school.group_school_id);
        fillCensusLocationFromSchool(
            school,
            locationTree,
            provinceSelect,
            districtSelect,
            communeSelect,
            villageSelect
        );
    });

    document.querySelectorAll('.teacher-dropdown').forEach(dropdown => {
        const label = dropdown.querySelector('.teacher-dropdown-label');
        const checkboxes = dropdown.querySelectorAll('input[name="teacher_id[]"]');
        const updateLabel = () => {
            const names = Array.from(dropdown.querySelectorAll('input[name="teacher_id[]"]:checked'))
                .map(checkbox => checkbox.parentElement.querySelector('label').textContent.trim());
            const selectedNames = names.join(', ');

            label.textContent = selectedNames || @json(__('Select teachers'));
            if (selectedNames) {
                label.title = selectedNames;
            } else {
                label.removeAttribute('title');
            }
        };

        checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateLabel));
    });
</script>
@include('census.partials.school-address-autofill')
@endsection
