@extends('layouts.app')

@section('title', 'Census Records')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Census Records</h1>
            <p class="text-secondary mb-0">View and manage census information</p>
        </div>
        <a href="{{ route('census.create') }}" class="btn btn-primary">+ Create Census</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @php($hasRecords = ($censusRecords ?? collect())->isNotEmpty())

    @if (! $hasRecords)
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-secondary py-5">No census records found.</div>
        </div>
    @else
        <div class="d-flex justify-content-end mb-3" role="group" aria-label="Choose record view">
            <div class="btn-group">
                <button class="btn btn-outline-primary active" type="button" data-view="cards">Cards</button>
                <button class="btn btn-outline-primary" type="button" data-view="list">List</button>
            </div>
        </div>

        <div id="cardsView" class="row g-3">
            @foreach ($censusRecords as $record)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between gap-2 mb-3">
                                <strong class="fs-5">{{ $record->name }}</strong>
                                <span class="badge text-bg-primary align-self-start">{{ $record->year }}</span>
                            </div>
                            <dl class="row mb-0 small">
                                <dt class="col-4 text-secondary">School</dt>
                                <dd class="col-8 text-end">{{ $record->school_name }}</dd>
                                <dt class="col-4 text-secondary">Teacher</dt>
                                <dd class="col-8 text-end">{{ $record->teacher_name }}</dd>
                                <dt class="col-4 text-secondary">Village</dt>
                                <dd class="col-8 text-end">{{ $record->village_name }}</dd>
                            </dl>
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
                                <th>Year</th>
                                <th>Name</th>
                                <th>School</th>
                                <th>Teacher</th>
                                <th>Village</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($censusRecords as $record)
                                <tr>
                                    <td>{{ $record->year }}</td>
                                    <td><strong>{{ $record->name }}</strong></td>
                                    <td>{{ $record->school_name }}</td>
                                    <td>{{ $record->teacher_name }}</td>
                                    <td>{{ $record->village_name }}</td>
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
