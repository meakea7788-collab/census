@extends('layouts.app')

@section('content')
<div class="container">
	<h1>{{ __('Delete School') }}</h1>

	<p>{{ __('Are you sure you want to delete this school?') }}</p>

	<form action="{{ route('schools.destroy', $school->id) }}" method="POST">
		@csrf
		@method('DELETE')

		<button type="submit">{{ __('Delete School') }}</button>
		<a href="{{ route('schools.index') }}">{{ __('Cancel') }}</a>
	</form>
</div>
@endsection
