@extends('layouts.app')

@section('content')
<div class="container">
	<h1>{{ __('Delete Teacher') }}</h1>

	<p>{{ __('Are you sure you want to delete :name?', ['name' => $teacher->name ?? __('this teacher')]) }}</p>

	<form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST">
		@csrf
		@method('DELETE')

		<button type="submit">{{ __('Delete Teacher') }}</button>
		<a href="{{ route('teachers.index') }}">{{ __('Cancel') }}</a>
	</form>
</div>
@endsection
