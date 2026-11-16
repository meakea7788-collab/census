@extends('layouts.app')

@section('title', __('Census Records'))

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <div>
                <h1 class="h3 mb-1">{{ __('Census Records') }}</h1>
                <p class="text-secondary mb-0">{{ __('View and manage census information') }}</p>
            </div>
            @if (isset($years) && $years->isNotEmpty())
                <form method="GET" action="{{ route('census.index') }}" class="m-0">
                    <label for="census-year-filter" class="visually-hidden">{{ __('Select year') }}</label>
                    <select id="census-year-filter" name="year_id" class="form-select" style="min-width: 160px;" onchange="this.form.submit()">
                        <option value="">{{ __('All years') }}</option>
                        @foreach ($years as $yearOption)
                            <option value="{{ $yearOption->id }}" @selected((string) ($yearId ?? '') === (string) $yearOption->id)>
                                {{ $yearOption->year }}{{ $yearOption->description ? ' - ' . $yearOption->description : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if (isset($activeYears) && $activeYears->isNotEmpty())
                <form method="POST" action="{{ route('census.carry-over') }}" class="d-flex align-items-center gap-2">
                    @csrf
                    <select name="year_id" class="form-select" style="min-width: 150px;" required>
                        @foreach ($activeYears as $activeYear)
                            <option value="{{ $activeYear->id }}" @selected((string) ($yearId ?? '') === (string) $activeYear->id)>
                                {{ $activeYear->year }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-success text-nowrap">
                        {{ __('Keep Data for Selected Year') }}
                    </button>
                </form>
            @endif
            <a href="{{ route('census.create') }}" class="btn btn-primary text-nowrap">{{ __('+ Create Census') }}</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @php
        $hasRecords = ($censusRecords ?? collect())->isNotEmpty();
    @endphp

    @if (! $hasRecords)
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-secondary py-5">{{ __('No census records found.') }}</div>
        </div>
    @else
        <div class="d-flex justify-content-end mb-3" role="group" aria-label="{{ __('Choose record view') }}">
            <div class="btn-group">
                <button class="btn btn-outline-primary active" type="button" data-view="cards">{{ __('Cards') }}</button>
                <button class="btn btn-outline-primary" type="button" data-view="list">{{ __('List') }}</button>
            </div>
        </div>

        <div id="cardsView" class="row g-3">
            @foreach ($censusRecords as $record)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between gap-2 mb-3">
                                <div>
                                    <strong class="fs-5">{{ $record->year }}</strong>
                                    @if ($record->year_description)
                                        <div class="small text-secondary">{{ $record->year_description }}</div>
                                    @endif
                                </div>
                                <span class="badge text-bg-primary align-self-start">{{ __('Year') }}</span>
                            </div>
                            <dl class="row mb-3 small">
                                <dt class="col-4 text-secondary">{{ __('School') }}</dt>
                                <dd class="col-8 text-end">{{ $record->school_name }}</dd>
                                <dt class="col-4 text-secondary">{{ __('Teacher') }}</dt>
                                <dd class="col-8 text-end">{{ $record->teacher_name }}</dd>
                                <dt class="col-4 text-secondary">{{ __('Village') }}</dt>
                                <dd class="col-8 text-end">{{ $record->village_name }}</dd>
                                <dt class="col-4 text-secondary">{{ __('Hole Houses') }}</dt>
                                <dd class="col-8 text-end">{{ $record->houses_count ?? 0 }}</dd>
                            </dl>

                            <div class="mt-auto d-flex gap-2">
                                <a href="{{ route('census.show', $record->id) }}" class="btn btn-sm btn-outline-info">{{ __('Detail') }}</a>
                                <a href="{{ route('census.edit', $record->id) }}" class="btn btn-sm btn-outline-warning">{{ __('Edit') }}</a>
                                <form action="{{ route('census.destroy', $record->id) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('Delete this census record?')));">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="listView" class="card border-0 shadow-sm d-none">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Year') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('School') }}</th>
                                <th>{{ __('Teacher') }}</th>
                                <th>{{ __('Village') }}</th>
                                <th>{{ __('Hole Houses') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($censusRecords as $record)
                                <tr>
                                    <td>{{ $record->year }}</td>
                                    <td>{{ $record->year_description ?: '—' }}</td>
                                    <td>{{ $record->school_name }}</td>
                                    <td>{{ $record->teacher_name }}</td>
                                    <td>{{ $record->village_name }}</td>
                                    <td>{{ $record->houses_count ?? 0 }}</td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('census.show', $record->id) }}" class="btn btn-sm btn-outline-info">{{ __('Detail') }}</a>
                                            <a href="{{ route('census.edit', $record->id) }}" class="btn btn-sm btn-outline-warning">{{ __('Edit') }}</a>
                                            <form action="{{ route('census.destroy', $record->id) }}" method="POST" onsubmit="return confirm(@js(__('Delete this census record?')));">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.querySelectorAll('[data-view]').forEach(button => {
        button.addEventListener('click', () => {
            const showCards = button.dataset.view === 'cards';
            document.querySelectorAll('[data-view]').forEach(item => item.classList.toggle('active', item === button));
            document.getElementById('cardsView')?.classList.toggle('d-none', !showCards);
            document.getElementById('listView')?.classList.toggle('d-none', showCards);
        });
    });
</script>
@endsection
