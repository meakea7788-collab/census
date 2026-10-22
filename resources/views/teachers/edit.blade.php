@extends('layouts.app')

@section('content')
<div class="container">
	<h1>Edit Teacher</h1>

	<form action="{{ route('teachers.update', $teacher->id) }}" method="POST">
		@csrf
		@method('PUT')

		<div class="mb-3">
			<label for="name" class="form-label">Name</label>
			<input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
				   value="{{ old('name', $teacher->name) }}" required>
			@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
		</div>

		<div class="mb-3">
			<label for="email" class="form-label">Email</label>
			<input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
				   value="{{ old('email', $teacher->email) }}" required>
			@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
		</div>

		<div class="mb-3">
			<label for="phone" class="form-label">Phone</label>
			<input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
				   value="{{ old('phone', $teacher->phone) }}">
			@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
		</div>

		<div class="mb-3">
			<label for="address" class="form-label">Address</label>
			<textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $teacher->address) }}</textarea>
			@error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
		</div>

		<button type="submit" class="btn btn-primary">Update Teacher</button>
		<a href="{{ route('teachers.index') }}" class="btn btn-secondary">Cancel</a>
	</form>
</div>
@endsection
