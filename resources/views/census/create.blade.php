@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Add Census') }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('census.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="year_id" class="form-label">{{ __('Year') }}</label>
            <select id="year_id" name="year_id" class="form-control" required>
                <option value="">{{ __('Select year') }}</option>
                @foreach ($years as $year)
                    <option value="{{ $year->id }}" @selected(old('year_id') == $year->id)>
                        {{ $year->year }}{{ $year->description ? ' - ' . $year->description : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="province_id" class="form-label">{{ __('Province') }}</label>
            <select id="province_id" name="province_id" class="form-control" required>
                <option value="">{{ __('Select province') }}</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province->id }}" @selected(old('province_id', $defaultLocation['province_id'] ?? '') == $province->id)>
                        {{ $province->name }}
                    </option>
                @endforeach
            </select>
        </div>
         <div class="mb-3">
            <label for="district_id" class="form-label">{{ __('District') }}</label>
            <select id="district_id" name="district_id" class="form-control" required>
                <option value="">{{ __('Select district') }}</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}" @selected(old('district_id') == $district->id)>
                        {{ $district->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="commune_id" class="form-label">{{ __('Commune') }}</label>
            <select id="commune_id" name="commune_id" class="form-control" required>
                <option value="">{{ __('Select commune') }}</option>
                @foreach ($communes as $commune)
                    <option value="{{ $commune->id }}" @selected(old('commune_id') == $commune->id)>
                        {{ $commune->name }}
                    </option>
                @endforeach
            </select>
        </div>
         <div class="mb-3">
            <label for="village_id" class="form-label">{{ __('Village') }}</label>
            <select id="village_id" name="village_id" class="form-control" required>
                <option value="">{{ __('Select village') }}</option>
                @foreach ($villages as $village)
                    <option value="{{ $village->id }}" @selected(old('village_id') == $village->id)>
                        {{ $village->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="group_school_id" class="form-label">{{ __('School Group') }}</label>
            <select id="group_school_id" @unless ($adminSchool) name="group_school_id" @endunless class="form-control" required @if ($adminSchool) disabled @endif>
                <option value="">{{ __('Select school group') }}</option>
                {{-- Options are filtered with JavaScript from the selected commune. --}}
            </select>
            @if ($adminSchool)
                <input type="hidden" name="group_school_id" value="{{ $adminSchool->group_school_id }}">
            @endif
        </div>

        <div class="mb-3">
            <label for="school_id" class="form-label">{{ __('School') }}</label>
            <select id="school_id" @unless ($adminSchool) name="school_id" @endunless class="form-control" required @if ($adminSchool) disabled @endif>
                <option value="">{{ __('Select school') }}</option>
                {{-- Options are filtered with JavaScript from the selected group school. --}}
            </select>
            @if ($adminSchool)
                <input type="hidden" name="school_id" value="{{ $adminSchool->id }}">
            @endif
        </div>

        <fieldset class="mb-3">
            <legend class="form-label">{{ __('Teachers') }}</legend>
            <details class="teacher-dropdown" id="teacher_dropdown">
                <summary class="form-select" id="teacher_dropdown_label">{{ __('Select a school to show its teachers.') }}</summary>
                <div id="teacher_list" class="teacher-dropdown-menu"></div>
            </details>
            @error('teacher_id')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
            @error('teacher_id.*')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </fieldset>

        <div class="mb-3">
            <label for="note" class="form-label">{{ __('Note') }}</label>
            <textarea id="note" name="note" class="form-control"
                      rows="4">{{ old('note') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        <a href="{{ route('census.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>

<script>
        const locationTree = @json($locationTree);
        const groupSchoolOptions = @json($groupSchoolOptions);
        const schoolOptions = @json($schoolOptions);
        const teacherOptions = @json($teacherOptions);
        const isAdmin = @json($adminSchool !== null);
        const adminSchoolId = @json($adminSchool?->id);
        const adminGroupSchoolId = @json($adminSchool?->group_school_id);

        const provinceSelect = document.getElementById('province_id');
        const districtSelect = document.getElementById('district_id');
        const communeSelect = document.getElementById('commune_id');
        const villageSelect = document.getElementById('village_id');
        const groupSchoolSelect = document.getElementById('group_school_id');
        const schoolSelect = document.getElementById('school_id');
        const teacherList = document.getElementById('teacher_list');
        const teacherDropdownLabel = document.getElementById('teacher_dropdown_label');
        const oldSelection = {
            province: @json(old('province_id', $defaultLocation['province_id'] ?? '')),
            district: @json(old('district_id', $defaultLocation['district_id'] ?? '')),
            commune: @json(old('commune_id', $defaultLocation['commune_id'] ?? '')),
            village: @json(old('village_id', $defaultLocation['village_id'] ?? '')),
            groupSchool: @json(old('group_school_id', $adminSchool?->group_school_id ?? '')),
            school: @json(old('school_id', $adminSchool?->id ?? '')),
            teachers: @json(old('teacher_id', [])),
        };

        function filterTeachers() {
            const schoolId = schoolSelect.value;
            const selectedTeachers = oldSelection.teachers.map(String);
            teacherList.replaceChildren();

            if (!schoolId) {
                teacherDropdownLabel.textContent = @json(__('Select a school to show its teachers.'));
                teacherDropdownLabel.removeAttribute('title');
                return;
            }

            const teachers = teacherOptions.filter(teacher => String(teacher.school_id) === String(schoolId));
            if (teachers.length === 0) {
                teacherDropdownLabel.textContent = @json(__('No teachers in this school.'));
                teacherDropdownLabel.removeAttribute('title');
                return;
            }

            teachers.forEach(teacher => {
                const wrapper = document.createElement('div');
                wrapper.className = 'form-check';

                const input = document.createElement('input');
                input.className = 'form-check-input';
                input.type = 'checkbox';
                input.name = 'teacher_id[]';
                input.id = `teacher_${teacher.id}`;
                input.value = teacher.id;
                input.checked = selectedTeachers.includes(String(teacher.id));
                input.addEventListener('change', () => {
                    oldSelection.teachers = Array.from(teacherList.querySelectorAll('input:checked'))
                        .map(selected => selected.value);
                    updateTeacherDropdownLabel(teachers);
                });

                const label = document.createElement('label');
                label.className = 'form-check-label';
                label.htmlFor = input.id;
                label.textContent = teacher.name;

                wrapper.append(input, label);
                teacherList.appendChild(wrapper);
            });

            const selectedForSchool = teachers
                .filter(teacher => selectedTeachers.includes(String(teacher.id)))
                .map(teacher => String(teacher.id));
            oldSelection.teachers = selectedForSchool;
            updateTeacherDropdownLabel(teachers);
        }

        function updateTeacherDropdownLabel(teachers) {
            const names = Array.from(teacherList.querySelectorAll('input:checked'))
                .map(input => teachers.find(teacher => String(teacher.id) === input.value)?.name)
                .filter(Boolean);
            const selectedNames = names.join(', ');

            teacherDropdownLabel.textContent = selectedNames || @json(__('Select teachers'));
            if (selectedNames) {
                teacherDropdownLabel.title = selectedNames;
            } else {
                teacherDropdownLabel.removeAttribute('title');
            }
        }

        function setOptions(select, items, placeholder, selectedId = '') {
            select.replaceChildren(new Option(placeholder, ''));
            items.forEach(item => select.add(new Option(item.name, item.id)));
            select.disabled = items.length === 0;
            select.value = items.some(item => String(item.id) === String(selectedId)) ? selectedId : '';
        }

        function selectedProvince() {
            return locationTree.find(item => String(item.id) === provinceSelect.value);
        }

        function selectedDistrict() {
            return selectedProvince()?.districts.find(item => String(item.id) === districtSelect.value);
        }

        function selectedCommune() {
            return selectedDistrict()?.communes.find(item => String(item.id) === communeSelect.value);
        }

        function filterGroupSchools() {
            const commune = selectedCommune();
            const communeName = (commune?.name ?? '').toLowerCase();
            const selectedSchool = schoolOptions.find(school => String(school.id) === String(schoolSelect.value));
            const groups = isAdmin
                ? groupSchoolOptions.filter(group => String(group.id) === String(adminGroupSchoolId))
                : communeName
                ? groupSchoolOptions.filter(group =>
                    String(group.id) === String(selectedSchool?.group_school_id) ||
                    (group.address || '').toLowerCase().includes(communeName)
                )
                : groupSchoolOptions;

            setOptions(groupSchoolSelect, groups, @json(__('Select school group')), oldSelection.groupSchool);
            if (isAdmin) {
                groupSchoolSelect.disabled = true;
            }
            filterSchools();
        }

        function filterSchools() {
            const groupId = groupSchoolSelect.value;
            const schools = isAdmin
                ? schoolOptions.filter(school => String(school.id) === String(adminSchoolId))
                : groupId
                ? schoolOptions.filter(school => String(school.group_school_id) === String(groupId))
                : [];
            setOptions(schoolSelect, schools, groupId ? @json(__('Select school')) : @json(__('Select school group first')), oldSelection.school);
            if (isAdmin) {
                schoolSelect.disabled = true;
            }
            filterTeachers();
        }

        provinceSelect.addEventListener('change', () => {
            setOptions(districtSelect, selectedProvince()?.districts ?? [], @json(__('Select district')));
            setOptions(communeSelect, [], @json(__('Select commune')));
            setOptions(villageSelect, [], @json(__('Select village')));
            setOptions(groupSchoolSelect, [], @json(__('Select school group')));
            setOptions(schoolSelect, [], @json(__('Select school group first')));
            oldSelection.teachers = [];
            filterTeachers();
        });

        districtSelect.addEventListener('change', () => {
            setOptions(communeSelect, selectedDistrict()?.communes ?? [], @json(__('Select commune')));
            setOptions(villageSelect, [], @json(__('Select village')));
            setOptions(groupSchoolSelect, [], @json(__('Select school group')));
            setOptions(schoolSelect, [], @json(__('Select school group first')));
            oldSelection.teachers = [];
            filterTeachers();
        });

        communeSelect.addEventListener('change', () => {
            setOptions(villageSelect, selectedCommune()?.villages ?? [], @json(__('Select village')));
            filterGroupSchools();
        });

        groupSchoolSelect.addEventListener('change', filterSchools);
        schoolSelect.addEventListener('change', filterTeachers);
        schoolSelect.addEventListener('change', () => {
            const school = schoolOptions.find(item => String(item.id) === String(schoolSelect.value));
            if (!school) return;

            oldSelection.school = String(school.id);
            oldSelection.groupSchool = String(school.group_school_id);
            if (fillCensusLocationFromSchool(
                school,
                locationTree,
                provinceSelect,
                districtSelect,
                communeSelect,
                villageSelect
            )) {
                oldSelection.province = String(school.location?.province_id ?? provinceSelect.value);
                oldSelection.district = String(school.location?.district_id ?? districtSelect.value);
                oldSelection.commune = String(school.location?.commune_id ?? communeSelect.value);
                oldSelection.village = String(school.location?.village_id ?? villageSelect.value);
                filterGroupSchools();
            }
        });

        setOptions(districtSelect, selectedProvince()?.districts ?? [], @json(__('Select district')), oldSelection.district);
        setOptions(communeSelect, selectedDistrict()?.communes ?? [], @json(__('Select commune')), oldSelection.commune);
        setOptions(villageSelect, selectedCommune()?.villages ?? [], @json(__('Select village')), oldSelection.village);
        filterGroupSchools();
</script>
@include('census.partials.school-address-autofill')
@endsection
