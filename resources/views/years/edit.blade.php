@extends('layouts.app')

@section('title', __('Edit Year'))

@section('content')
    <div class="container-fluid p-0">
        <div class="mb-4">
            <h1 class="h3 mb-1">{{ __('Edit Year') }}</h1>
            <p class="text-secondary mb-0">{{ __('Update year details and status.') }}</p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('years.update', $year->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="year" class="form-label">{{ __('Year') }}</label>
                            <input id="year" name="year" type="number" min="1900" max="2100"
                                   class="form-control @error('year') is-invalid @enderror"
                                   value="{{ old('year', $year->year) }}" required>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <input id="description" name="description" type="text" maxlength="255"
                                   class="form-control @error('description') is-invalid @enderror"
                                   value="{{ old('description', $year->description) }}">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input id="is_active" name="is_active" type="checkbox" value="1"
                                       class="form-check-input @error('is_active') is-invalid @enderror"
                                       @checked(old('is_active', $year->is_active))>
                                <label for="is_active" class="form-check-label">{{ __('Active') }}</label>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">{{ __('Update Year') }}</button>
                        <a href="{{ route('years.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection