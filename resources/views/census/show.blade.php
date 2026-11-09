@extends('layouts.app')

@section('title', __('Census Details'))

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">{{ $census->name }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('census.edit', $census->id) }}" class="btn btn-warning">{{ __('Edit') }}</a>
            <a href="{{ route('census.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('Year') }}</dt>
                <dd class="col-sm-9">{{ $year?->year ?? $year?->name ?? '-' }}</dd>

                <dt class="col-sm-3">{{ __('Group School') }}</dt>
                <dd class="col-sm-9">{{ $groupSchool?->name ?? '-' }}</dd>

                <dt class="col-sm-3">{{ __('School') }}</dt>
                <dd class="col-sm-9">{{ $school?->name ?? '-' }}</dd>

                <dt class="col-sm-3">{{ __('Province') }}</dt>
                <dd class="col-sm-9">{{ $province?->name ?? '-' }}</dd>

                <dt class="col-sm-3">{{ __('District') }}</dt>
                <dd class="col-sm-9">{{ $district?->name ?? '-' }}</dd>

                <dt class="col-sm-3">{{ __('Commune') }}</dt>
                <dd class="col-sm-9">{{ $commune?->name ?? '-' }}</dd>

                <dt class="col-sm-3">{{ __('Village') }}</dt>
                <dd class="col-sm-9">{{ $village?->name ?? '-' }}</dd>

                <dt class="col-sm-3">{{ __('Teachers') }}</dt>
                <dd class="col-sm-9">
                    @if ($teacher->isEmpty())
                        -
                    @else
                        {{ $teacher->implode(', ') }}
                    @endif
                </dd>

                <dt class="col-sm-3">{{ __('Note') }}</dt>
                <dd class="col-sm-9">{{ $census->note ?: '-' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
