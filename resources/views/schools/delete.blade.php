@extends('layouts.app')

@section('content')
<div class="container">
	<h1>Delete School</h1>

	<p>Are you sure you want to delete this school?</p>

	<form action="{{ route('schools.destroy', $school->id) }}" method="POST">
		@csrf
		@method('DELETE')

		<button type="submit">Delete School</button>
		<a href="{{ route('schools.index') }}">Cancel</a>
	</form>
</div>
@endsection

