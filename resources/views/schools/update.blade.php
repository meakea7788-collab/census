@extends('layouts.app')

@section('content')
<div class="container py-4">
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">Update School</div>
				<div class="card-body">
					<form method="POST" action="{{ route('schools.update', $school) }}">
						@csrf
						@method('PUT')

						@foreach ($school->getAttributes() as $field => $value)
							@continue(in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at', 'password', 'remember_token']))

							@php
								$label = \Illuminate\Support\Str::headline($field);
								$inputType = str_contains(strtolower($field), 'email') ? 'email' : (str_contains(strtolower($field), 'phone') ? 'tel' : 'text');
								$isLongText = str_contains(strtolower($field), 'address') || str_contains(strtolower($field), 'description');
							@endphp

							<div class="mb-3">
								<label for="{{ $field }}" class="form-label">{{ $label }}</label>
								@if ($isLongText)
									<textarea id="{{ $field }}" name="{{ $field }}" rows="3" class="form-control @error($field) is-invalid @enderror">{{ old($field, $value) }}</textarea>
								@else
									<input id="{{ $field }}" name="{{ $field }}" type="{{ $inputType }}" value="{{ old($field, $value) }}" class="form-control @error($field) is-invalid @enderror">
								@endif
								@error($field)
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						@endforeach

						<button type="submit" class="btn btn-primary">Save changes</button>
						<a href="{{ route('schools.index') }}" class="btn btn-secondary">Cancel</a>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
