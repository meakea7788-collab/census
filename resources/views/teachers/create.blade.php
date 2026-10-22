@extends('layouts.app')

@section('content')
<div class="container">
	<h1 class="mb-4">Add Teacher</h1>

	<form action="{{ route('teachers.store') }}" method="POST">
		@csrf

		<div class="mb-3">
			<label for="name" class="form-label">Full name</label>
			<input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
				   value="{{ old('name') }}" required>
			@error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
		</div>

		<div class="mb-3">
			<label for="sex" class="form-label">Sex</label>
			<select name="sex" id="sex" class="form-control @error('sex') is-invalid @enderror" required>
				<option value="">Select Sex</option>
				<option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
				<option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
			</select>
			@error('sex') <div class="invalid-feedback">{{ $message }}</div> @enderror
		</div>

		<div class="mb-3">
			<label for="phone" class="form-label">Phone</label>
			<input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
				   value="{{ old('phone') }}">
			@error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
		</div>

		<div class="mb-3">
			<label for="position" class="form-label">Position</label>
			<input type="text" name="position" id="position" class="form-control @error('position') is-invalid @enderror"
				   value="{{ old('position') }}">
			@error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
		</div>
        <div class="mb-3">
            <label for="school_id" class="form-label">School</label>
            <select name="school_id" id="school_id" class="form-control @error('school_id') is-invalid @enderror" required>
                <option value="">Select School</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
            @error('school_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
		<button type="submit" class="btn btn-primary">Save Teacher</button>
		<a href="{{ route('teachers.index') }}" class="btn btn-secondary">Cancel</a>
	</form>
</div>
@endsection
