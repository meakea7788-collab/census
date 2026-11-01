@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Census</h1>

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
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                   class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="year_id" class="form-label">Year</label>
            <select id="year_id" name="year_id" class="form-control" required>
                <option value="">Select year</option>
                @foreach ($years as $year)
                    <option value="{{ $year->id }}" @selected(old('year_id') == $year->id)>
                        {{ $year->name ?? $year->year }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="province_id" class="form-label">Province</label>
            <select id="province_id" name="province_id" class="form-control" required>
                <option value="">Select province</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province->id }}" @selected(old('province_id') == $province->id)>
                        {{ $province->name }}
                    </option>
                @endforeach
            </select>
        </div>
         <div class="mb-3">
            <label for="district_id" class="form-label">District</label>
            <select id="district_id" name="district_id" class="form-control" required>
                <option value="">Select district</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}" @selected(old('district_id') == $district->id)>
                        {{ $district->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="commune_id" class="form-label">Commune</label>
            <select id="commune_id" name="commune_id" class="form-control" required>
                <option value="">Select commune</option>
                @foreach ($communes as $commune)
                    <option value="{{ $commune->id }}" @selected(old('commune_id') == $commune->id)>
                        {{ $commune->name }}
                    </option>
                @endforeach
            </select>
        </div>
         <div class="mb-3">
            <label for="village_id" class="form-label">Village</label>
            <select id="village_id" name="village_id" class="form-control" required>
                <option value="">Select village</option>
                @foreach ($villages as $village)
                    <option value="{{ $village->id }}" @selected(old('village_id') == $village->id)>
                        {{ $village->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="group_school_id" class="form-label">School Group</label>
            <select id="group_school_id" name="group_school_id" class="form-control" required>
                <option value="">Select school group</option>
                {{-- Options are filtered with JavaScript from the selected commune. --}}
            </select>
        </div>

        <div class="mb-3">
            <label for="school_id" class="form-label">School</label>
            <select id="school_id" name="school_id" class="form-control" required>
                <option value="">Select school</option>
                {{-- Options are filtered with JavaScript from the selected group school. --}}
            </select>
        </div>

        <fieldset class="mb-3">
            <legend class="form-label">Teachers</legend>
            <div id="teacher_list" class="border rounded p-3" style="max-height: 240px; overflow-y: auto;">
                <span class="text-muted">Select a school to show its teachers.</span>
            </div>
            @error('teacher_id')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </fieldset>

        <div class="mb-3">
            <label for="note" class="form-label">Note</label>
            <textarea id="note" name="note" class="form-control"
                      rows="4">{{ old('note') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('census.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
        const locationTree = @json($locationTree);
        const groupSchoolOptions = @json($groupSchoolOptions);
        const schoolOptions = @json($schoolOptions);
        const teacherOptions = @json($teacherOptions);

        const provinceSelect = document.getElementById('province_id');
        const districtSelect = document.getElementById('district_id');
        const communeSelect = document.getElementById('commune_id');
        const villageSelect = document.getElementById('village_id');
        const groupSchoolSelect = document.getElementById('group_school_id');
        const schoolSelect = document.getElementById('school_id');
        const teacherList = document.getElementById('teacher_list');
        const oldSelection = {
            province: @json(old('province_id')),
            district: @json(old('district_id')),
            commune: @json(old('commune_id')),
            village: @json(old('village_id')),
            groupSchool: @json(old('group_school_id')),
            school: @json(old('school_id')),
            teachers: @json(old('teacher_id', [])),
        };

        function filterTeachers() {
            const schoolId = schoolSelect.value;
            const selectedTeachers = oldSelection.teachers.map(String);
            teacherList.replaceChildren();

            if (!schoolId) {
                teacherList.innerHTML = '<span class="text-muted">Select a school to show its teachers.</span>';
                return;
            }

            const teachers = teacherOptions.filter(teacher => String(teacher.school_id) === String(schoolId));
            if (teachers.length === 0) {
                teacherList.innerHTML = '<span class="text-muted">No teachers in this school.</span>';
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

                const label = document.createElement('label');
                label.className = 'form-check-label';
                label.htmlFor = input.id;
                label.textContent = teacher.name;

                wrapper.append(input, label);
                teacherList.appendChild(wrapper);
            });
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
            const groups = communeName
                ? groupSchoolOptions.filter(group => (group.address || '').toLowerCase().includes(communeName))
                : [];

            setOptions(groupSchoolSelect, groups, commune ? 'Select school group' : 'Select commune first', oldSelection.groupSchool);
            filterSchools();
        }

        function filterSchools() {
            const groupId = groupSchoolSelect.value;
            const schools = groupId
                ? schoolOptions.filter(school => String(school.group_school_id) === String(groupId))
                : [];
            setOptions(schoolSelect, schools, groupId ? 'Select school' : 'Select school group first', oldSelection.school);
            filterTeachers();
        }

        provinceSelect.addEventListener('change', () => {
            setOptions(districtSelect, selectedProvince()?.districts ?? [], 'Select district');
            setOptions(communeSelect, [], 'Select commune');
            setOptions(villageSelect, [], 'Select village');
            setOptions(groupSchoolSelect, [], 'Select commune first');
            setOptions(schoolSelect, [], 'Select school group first');
        });

        districtSelect.addEventListener('change', () => {
            setOptions(communeSelect, selectedDistrict()?.communes ?? [], 'Select commune');
            setOptions(villageSelect, [], 'Select village');
            setOptions(groupSchoolSelect, [], 'Select commune first');
            setOptions(schoolSelect, [], 'Select school group first');
        });

        communeSelect.addEventListener('change', () => {
            setOptions(villageSelect, selectedCommune()?.villages ?? [], 'Select village');
            filterGroupSchools();
        });

        groupSchoolSelect.addEventListener('change', filterSchools);
        schoolSelect.addEventListener('change', filterTeachers);

        setOptions(districtSelect, selectedProvince()?.districts ?? [], 'Select district', oldSelection.district);
        setOptions(communeSelect, selectedDistrict()?.communes ?? [], 'Select commune', oldSelection.commune);
        setOptions(villageSelect, selectedCommune()?.villages ?? [], 'Select village', oldSelection.village);
        filterGroupSchools();
</script>
@endsection
