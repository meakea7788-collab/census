@extends('layouts.app')

@section('content')
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1>{{ __('Teachers') }}</h1>
		<a href="{{ route('teachers.create') }}" class="btn btn-primary">{{ __('Add Teacher') }}</a>
	</div>

	@if (session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif

	<div class="table-responsive">
		<table class="table table-striped table-bordered align-middle">
			<thead>
				<tr>
					<th>{{ __('Name') }}</th>
					<th>{{ __('Sex') }}</th>
					<th>{{ __('Position') }}</th>
					<th>{{ __('Phone') }}</th>
					<th>{{ __('School') }}</th>
                    <th width="220">{{ __('Actions') }}</th>
				</tr>
			</thead>
			<tbody>
				@forelse ($teachers as $teacher)
					<tr>
						<td>{{ $teacher->name }}</td>
						<td>{{ __($teacher->sex) }}</td>
						<td>{{ $teacher->position }}</td>
						<td>{{ $teacher->phone }}</td>
						<td>{{ $teacher->school?->name ?? '—' }}</td>
                        <td>
                            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                            <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm(@js(__('Are you sure you want to delete this teacher?')))">{{ __('Delete') }}</button>
                            </form>
                        </td>
					</tr>
				@empty
					<tr>
						<td colspan="6" class="text-center">{{ __('No teachers found.') }}</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
@endsection
