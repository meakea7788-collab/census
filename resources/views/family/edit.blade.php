@extends('layouts.app')

@section('title', __('Edit Family Member'))

@section('content')
<div class="container py-4">
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-white py-3">
					<h1 class="h4 mb-0">{{ __('Edit Family Member') }}</h1>
				</div>
				<div class="card-body p-4">
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

					<form method="POST" action="{{ route('family.update', $family->id) }}">
						@csrf
						@method('PUT')

						<div class="mb-3">
							<label for="holehouse_id" class="form-label">{{ __('House') }} <span class="text-danger">*</span></label>
							<select id="holehouse_id" name="holehouse_id" class="form-select @error('holehouse_id') is-invalid @enderror" required>
								<option value="">{{ __('Select a house') }}</option>
								@foreach ($holeHouses as $holeHouse)
									<option value="{{ $holeHouse->id }}" @selected(old('holehouse_id', $family->holehouse_id) == $holeHouse->id)>
										{{ __('House #:number', ['number' => $holeHouse->hole_number]) }} — {{ $holeHouse->leader_family_name }}
									</option>
								@endforeach
							</select>
							@error('holehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>

						<div class="mb-3">
							<label for="family_name" class="form-label">{{ __('Family name') }} <span class="text-danger">*</span></label>
							<input type="text" id="family_name" name="family_name" value="{{ old('family_name', $family->family_name) }}" class="form-control @error('family_name') is-invalid @enderror" maxlength="255" required>
							@error('family_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="gender" class="form-label">{{ __('Gender') }} <span class="text-danger">*</span></label>
								<input type="text" id="gender" name="gender" value="{{ old('gender', $family->gender) }}" class="form-control @error('gender') is-invalid @enderror" maxlength="255" required>
								@error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
							<div class="col-md-6 mb-3">
								<label for="date_of_birth" class="form-label">{{ __('Date of birth') }} <span class="text-danger">*</span></label>
								<input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $family->date_of_birth) }}" class="form-control @error('date_of_birth') is-invalid @enderror" required>
								@error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="relationship" class="form-label">{{ __('Relationship') }} <span class="text-danger">*</span></label>
								<input type="text" id="relationship" name="relationship" value="{{ old('relationship', $family->relationship) }}" class="form-control @error('relationship') is-invalid @enderror" maxlength="255" required>
								@error('relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
							<div class="col-md-6 mb-3">
								<label for="job" class="form-label">{{ __('Job') }} <span class="text-danger">*</span></label>
								<input type="text" id="job" name="job" value="{{ old('job', $family->job) }}" class="form-control @error('job') is-invalid @enderror" maxlength="255" required>
								@error('job')<div class="invalid-feedback">{{ $message }}</div>@enderror
							</div>
						</div>

						<div class="d-flex justify-content-end gap-2 mt-3">
							<a href="{{ route('family.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
