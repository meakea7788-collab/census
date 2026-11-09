@extends('layouts.app')

@section('title', __('Record Family Member Move'))

@section('content')
<div class="container py-4">
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-white py-3">
					<h1 class="h4 mb-0">{{ __('Record Family Member Move') }}</h1>
				</div>
				<div class="card-body p-4">
					<p class="text-muted">
						{{ __('Record a new address for :name.', ['name' => $family->family_name]) }}
					</p>
					<p>{{ __('Current house') }}: {{ $family->holehouse?->hole_number ?? '—' }}</p>

					@if ($errors->any())
						<div class="alert alert-danger" role="alert">
							<strong>{{ __('Please correct the following:') }}</strong>
							<ul class="mb-0 mt-2">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form method="POST" action="{{ route('family.move.update', $family->id) }}">
						@csrf
						@method('PUT')

						<div class="mb-3">
							<label for="moved_to_address" class="form-label">{{ __('New address') }} <span class="text-danger">*</span></label>
							<textarea id="moved_to_address" name="moved_to_address" rows="4" maxlength="2000" class="form-control @error('moved_to_address') is-invalid @enderror" required>{{ old('moved_to_address', $family->moved_to_address) }}</textarea>
							@error('moved_to_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>

						<div class="d-flex justify-content-end gap-2">
							<a href="{{ route('family.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('Save new address') }}</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
