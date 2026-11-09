@extends('layouts.app')

@section('title', __('Create Address'))

@section('content')
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-8">
                <div class="mb-4">
                    <h1 class="h3 mb-2">{{ __('Create Address') }}</h1>
                    <p class="text-secondary mb-0">{{ __('Add a province and its district, commune, and village.') }}</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success" role="status">{{ session('success') }}</div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('provinces.store') }}">
                            @csrf

                            <div class="row g-3">
                                <div class="col-12">
                                    <h2 class="h5 mb-0">{{ __('Province') }}</h2>
                                </div>
                                <div class="col-12">
                                    <label for="province_name" class="form-label">{{ __('Province name') }}</label>
                                    <input
                                        type="text"
                                        class="form-control @error('province_name') is-invalid @enderror"
                                        id="province_name"
                                        name="province_name"
                                        value="{{ old('province_name') }}"
                                        maxlength="255"
                                        required
                                    >
                                    @error('province_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mt-4">
                                    <h2 class="h5 mb-0">{{ __('District') }}</h2>
                                </div>
                                <div class="col-12">
                                    <label for="district_name" class="form-label">{{ __('District name') }}</label>
                                    <input
                                        type="text"
                                        class="form-control @error('district_name') is-invalid @enderror"
                                        id="district_name"
                                        name="district_name"
                                        value="{{ old('district_name') }}"
                                        maxlength="255"
                                        required
                                    >
                                    @error('district_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mt-4">
                                    <h2 class="h5 mb-0">{{ __('Commune') }}</h2>
                                </div>
                                <div class="col-12">
                                    <label for="commune_name" class="form-label">{{ __('Commune name') }}</label>
                                    <input
                                        type="text"
                                        class="form-control @error('commune_name') is-invalid @enderror"
                                        id="commune_name"
                                        name="commune_name"
                                        value="{{ old('commune_name') }}"
                                        maxlength="255"
                                        required
                                    >
                                    @error('commune_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mt-4">
                                    <h2 class="h5 mb-0">{{ __('Village') }}</h2>
                                </div>
                                <div class="col-12">
                                    <label for="village_name" class="form-label">{{ __('Village name') }}</label>
                                    <input
                                        type="text"
                                        class="form-control @error('village_name') is-invalid @enderror"
                                        id="village_name"
                                        name="village_name"
                                        value="{{ old('village_name') }}"
                                        maxlength="255"
                                        required
                                    >
                                    @error('village_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">{{ __('Save address hierarchy') }}</button>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
