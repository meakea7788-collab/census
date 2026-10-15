<form action="{{ route('group-school.store') }}" method="POST">
	@csrf

	@if (session('status'))
		<div role="status">{{ session('status') }}</div>
	@endif

	<div>
		<label for="name">Group School Name</label>
		<input type="text" id="name" name="name" value="{{ old('name') }}" required>
		@error('name') <span>{{ $message }}</span> @enderror
	</div>

	<div>
		<label for="director_name">Director Name</label>
		<input type="text" id="director_name" name="director_name" value="{{ old('director_name') }}" required>
		@error('director_name') <span>{{ $message }}</span> @enderror
	</div>

	<div>
		<label for="phone">Phone</label>
		<input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required>
		@error('phone') <span>{{ $message }}</span> @enderror
	</div>

	<div>
		<label for="address">Address</label>
		<textarea id="address" name="address" required>{{ old('address') }}</textarea>
		@error('address') <span>{{ $message }}</span> @enderror
	</div>

	<button type="submit">Create Group School</button>
</form>
