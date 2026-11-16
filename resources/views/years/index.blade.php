@extends('layouts.app')

@section('title', __('Years'))

@section('content')
    <div class="container-fluid p-0">
        <div class="mb-4">
            <h1 class="h3 mb-1">{{ __('Years') }}</h1>
            <p class="text-secondary mb-0">{{ __('Add and manage census years.') }}</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if (auth()->user()?->role === \App\Models\User::ROLE_SUPERADMIN)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h2 class="h5 mb-0">{{ __('Add Year') }}</h2>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('years.store') }}" method="POST">
                        @csrf

                        <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-3">
                            <label for="year" class="form-label">{{ __('Year') }}</label>
                            <input id="year" name="year" type="number" min="1900" max="2100"
                                   class="form-control @error('year') is-invalid @enderror"
                                   value="{{ old('year') }}" placeholder="{{ __('e.g. 2026') }}" required>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <input id="description" name="description" type="text" maxlength="255"
                                   class="form-control @error('description') is-invalid @enderror"
                                   value="{{ old('description') }}" placeholder="{{ __('Optional description') }}">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="form-check mb-2">
                                <input id="is_active" name="is_active" type="checkbox" value="1"
                                       class="form-check-input @error('is_active') is-invalid @enderror"
                                       @checked(old('is_active', true))>
                                <label for="is_active" class="form-check-label">{{ __('Active') }}</label>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-check mb-2">
                                <input id="carry_over_data" name="carry_over_data" type="checkbox" value="1"
                                       class="form-check-input"
                                       @checked(old('carry_over_data', true))>
                                <label for="carry_over_data" class="form-check-label">{{ __('Keep census data from previous year') }}</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">{{ __('Add Year') }}</button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">{{ __('Year List') }}</h2>
            <span class="badge text-bg-secondary">{{ $years->count() }}</span>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">{{ __('Year') }}</th>
                                <th scope="col">{{ __('Description') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                                <th scope="col">{{ __('Active') }}</th>
                                <th scope="col" class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($years as $year)
                                <tr>
                                    <td class="fw-semibold">{{ $year->year }}</td>
                                    <td>{{ $year->description ?: '—' }}</td>
                                    <td>
                                        @if ($year->is_active)
                                            <span class="badge text-bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge text-bg-secondary">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('years.toggle-active', $year->id) }}" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_active" value="0">
                                            <div class="form-check form-switch m-0">
                                                <input
                                                    id="year-active-{{ $year->id }}"
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    role="switch"
                                                    name="is_active"
                                                    value="1"
                                                    style="cursor: pointer;"
                                                    @checked($year->is_active)
                                                    onchange="this.form.submit()"
                                                >
                                                <label class="visually-hidden" for="year-active-{{ $year->id }}">{{ __('Active') }}</label>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('years.edit', $year->id) }}"
                                           class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">
                                        {{ __('No years found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
