@extends('layouts.app')

@section('title', __('Edit User'))

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">{{ __('Edit User') }}</h1>
                <p class="text-secondary mb-0">{{ $user->name }}</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">{{ __('Back to Users') }}</a>
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

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="password" class="form-label">{{ __('New password (optional)') }}</label>
                            <input id="password" name="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="new-password">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="role" class="form-label">{{ __('Role') }}</label>
                            @if ($canAssignRoles)
                                <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    @foreach (\App\Models\User::roles() as $role)
                                        <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>
                                            {{ $role === \App\Models\User::ROLE_USER ? __('Normal User') : __(ucfirst($role)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @else
                                <input type="text" class="form-control" value="{{ __($user->role) }}" disabled>
                            @endif
                        </div>

                        @if ($canAssignRoles)
                            <div class="col-12 col-md-6">
                                <label for="school_id" class="form-label">{{ __('School') }}</label>
                                <select id="school_id" name="school_id" class="form-select @error('school_id') is-invalid @enderror" required>
                                    <option value="">{{ __('Select school') }}</option>
                                    @foreach ($schools as $school)
                                        <option value="{{ $school->id }}" @selected(old('school_id', $user->school_id) == $school->id)>
                                            {{ $school->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('school_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @else
                        <div class="col-12">
                            <fieldset class="border rounded p-3 @error('teacher_ids') border-danger @enderror">
                                <legend class="float-none w-auto px-2 fs-6 mb-1">
                                    {{ __('Teachers') }} @if ($requiresTeacher)<span class="text-danger">*</span>@endif
                                </legend>
                                <p class="form-text mt-0">{{ __('Select one or more teachers assigned to a census.') }}</p>
                                @php($selectedTeacherIds = collect(old('teacher_ids', old('teacher_id') ? [old('teacher_id')] : $selectedTeacherIds))->map(fn ($id) => (int) $id)->all())
                                <div class="row g-2">
                                    @forelse ($teachers as $teacher)
                                        @php($censusGroupNames = $teacher->censuses->pluck('groupSchool.name')->filter()->unique())
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <label class="d-flex align-items-start gap-2 border rounded p-2 h-100">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input mt-1 teacher-checkbox"
                                                    name="teacher_ids[]"
                                                    value="{{ $teacher->id }}"
                                                    @checked(in_array($teacher->id, $selectedTeacherIds, true))
                                                >
                                                <span>
                                                    <span class="d-block fw-semibold">{{ $teacher->name }}</span>
                                                    <span class="d-block small text-secondary">
                                                        {{ $censusGroupNames->isNotEmpty() ? $censusGroupNames->implode(', ') : ($teacher->school?->groupSchool?->name ?? '—') }}
                                                        @if ($teacher->school) · {{ $teacher->school->name }} @endif
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @empty
                                        <div class="col-12 text-secondary">{{ __('No teachers assigned to a census are available.') }}</div>
                                    @endforelse
                                </div>
                                @error('teacher_ids') <div class="text-danger small">{{ $message }}</div> @enderror
                                @error('teacher_ids.*') <div class="text-danger small">{{ $message }}</div> @enderror
                            </fieldset>
                        </div>
                        @endif
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection