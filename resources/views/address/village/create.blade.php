@extends('layouts.app')

@section('title', __('Add Village'))

@section('content')
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <div class="mb-4">
                    <h1 class="h3 mb-2">{{ __('Add Village') }}</h1>
                    <p class="text-secondary mb-0">{{ __('Add a village to this commune.') }}</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('communes.villages.store', $commune) }}">
                            @csrf

                            <div class="mb-4">
                                <label for="name" class="form-label">{{ __('Village name') }}</label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}"
                                    maxlength="255"
                                    required
                                    autofocus
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">{{ __('Save Village') }}</button>
                                <a href="{{ route('communes.show', $commune) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
