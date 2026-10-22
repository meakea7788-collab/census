@extends('layouts.app')

@section('content')
<div class="container">
	<h1>Delete Teacher</h1>

	<p>Are you sure you want to delete {{ $teacher->name ?? 'this teacher' }}?</p>

	<form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST">
		@csrf
		@method('DELETE')

		<button type="submit">Delete Teacher</button>
		<a href="{{ route('teachers.index') }}">Cancel</a>
	</form>
</div>
@endsection
