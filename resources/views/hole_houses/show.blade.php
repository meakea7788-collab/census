@extends('layouts.app')

@section('title', __('Hole House Details'))

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ __('Hole House Details') }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('family.create', ['holehouse_id' => $holeHouse->id]) }}" class="btn btn-success">{{ __('Add family member') }}</a>
            <a href="{{ route('hole_houses.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">{{ __('Hole number') }}</dt>
                <dd class="col-sm-9">{{ $holeHouse->hole_number }}</dd>

                <dt class="col-sm-3">{{ __('Household leader') }}</dt>
                <dd class="col-sm-9">{{ $holeHouse->leader_family_name }}</dd>

                <dt class="col-sm-3">{{ __('Phone number') }}</dt>
                <dd class="col-sm-9">{{ $holeHouse->phone_number }}</dd>

                <dt class="col-sm-3">{{ __('Village') }}</dt>
                <dd class="col-sm-9">{{ $holeHouse->village?->name ?? '—' }}</dd>

                <dt class="col-sm-3">{{ __('Additional address details') }}</dt>
                <dd class="col-sm-9">{{ $holeHouse->address ?: '—' }}</dd>

                <dt class="col-sm-3">{{ __('Status') }}</dt>
                <dd class="col-sm-9">{{ __($holeHouse->status) }}</dd>
            </dl>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h2 class="h4 mb-0">{{ __('Family members') }}</h2>
        <span class="badge text-bg-secondary">{{ $holeHouse->families->count() }}</span>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Family name') }}</th>
                        <th scope="col">{{ __('Gender') }}</th>
                        <th scope="col">{{ __('Date of birth') }}</th>
                        <th scope="col">{{ __('Relationship') }}</th>
                        <th scope="col">{{ __('Job') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col" class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($holeHouse->families as $familyMember)
                        <tr>
                            <td>{{ $familyMember->id }}</td>
                            <td class="fw-semibold">{{ $familyMember->family_name }}</td>
                            <td>{{ $familyMember->gender }}</td>
                            <td>{{ $familyMember->date_of_birth }}</td>
                            <td>{{ $familyMember->relationship }}</td>
                            <td>{{ $familyMember->job }}</td>
                            <td>
                                @if ($familyMember->is_deceased)
                                    <span class="badge text-bg-secondary">{{ __('Deceased') }}</span>
                                @elseif ($familyMember->moved_to_address)
                                    <span class="badge text-bg-info text-dark" title="{{ $familyMember->moved_to_address }}">{{ __('Moved') }}</span>
                                @else
                                    <span class="badge text-bg-success">{{ __('Active') }}</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('family.show', $familyMember->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Detail') }}" aria-label="{{ __('Detail') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8ZM1.17 8a13.1 13.1 0 0 1 1.66-2.1C4.16 4.5 5.91 3.5 8 3.5s3.84 1 5.17 2.4A13.1 13.1 0 0 1 14.83 8a13.1 13.1 0 0 1-1.66 2.1C11.84 11.5 10.09 12.5 8 12.5s-3.84-1-5.17-2.4A13.1 13.1 0 0 1 1.17 8Z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Zm-1.5 2.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z"/></svg>
                                </a>
                                <a href="{{ route('family.edit', $familyMember->id) }}" class="btn btn-sm btn-outline-warning" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M12.85.15a.5.5 0 0 1 .71 0l2.29 2.29a.5.5 0 0 1 0 .71l-9.5 9.5a.5.5 0 0 1-.22.13l-4 1a.5.5 0 0 1-.61-.61l1-4a.5.5 0 0 1 .13-.22l9.5-9.5ZM12.5 1.21 4.09 9.62l-.62 2.48 2.48-.62 8.41-8.41L12.5 1.21Z"/></svg>
                                </a>
                                @if (! $familyMember->is_deceased)
                                    <a href="{{ route('family.move.edit', $familyMember->id) }}" class="btn btn-sm btn-outline-info" title="{{ __('Record move address') }}" aria-label="{{ __('Record move address') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M1 8a.5.5 0 0 1 .5-.5h8.793L7.146 4.354a.5.5 0 1 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L10.293 8.5H1.5A.5.5 0 0 1 1 8Z"/><path d="M13.5 2A1.5 1.5 0 0 1 15 3.5v9a1.5 1.5 0 0 1-1.5 1.5H12a.5.5 0 0 1 0-1h1.5a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5H12a.5.5 0 0 1 0-1h1.5Z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('family.mark-deceased', $familyMember->id) }}" class="d-inline" onsubmit="return confirm(@js(__('Mark this family member as deceased?')));">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ __('Mark deceased') }}" aria-label="{{ __('Mark deceased') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M4 2a4 4 0 0 1 8 0v8H4V2zm-2 10a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1H2v-1zm6-8a.5.5 0 0 0-.5.5V6H6a.5.5 0 0 0 0 1h1.5v2a.5.5 0 0 0 1 0V7H10a.5.5 0 0 0 0-1H8.5V4.5A.5.5 0 0 0 8 4z"/></svg>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('family.destroy', $familyMember->id) }}" class="d-inline" onsubmit="return confirm(@js(__('Delete this family member?')));">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M5.5 5.5a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9.5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1 0-2H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1ZM4 4v9.5a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4H4Zm2.5-2a.5.5 0 0 0-.5.5V3h4v-.5a.5.5 0 0 0-.5-.5h-3Z"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">{{ __('No family members have been added to this house yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
