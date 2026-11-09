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
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name', $census->name) }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="year_id" class="form-label">{{ __('Year') }}</label>
                <select id="year_id" name="year_id" class="form-select" required>
                    <option value="">{{ __('Select year') }}</option>
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}" @selected(old('year_id', $census->year_id) == $year->id)>
                            {{ $year->year }}
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
                <div class="border rounded p-3">
                    @foreach ($teachers as $teacher)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="teacher_id[]" value="{{ $teacher->id }}" id="teacher_{{ $teacher->id }}" @checked(in_array($teacher->id, old('teacher_id', $teacherIds ?? [])))>
                            <label class="form-check-label" for="teacher_{{ $teacher->id }}">{{ $teacher->name }}</label>
                        </div>
                    @endforeach
                </div>
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
@endsection
