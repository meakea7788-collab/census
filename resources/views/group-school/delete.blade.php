<div class="container">
	<h1>Delete Group School</h1>

	<p>Are you sure you want to delete the group school
		<strong>{{ $groupSchool->name ?? 'this group school' }}</strong>?
		This action cannot be undone.
	</p>

	<form action="{{ route('group-school.destroy', $groupSchool->id) }}" method="POST">
		@csrf
		@method('DELETE')

		<button type="submit">Delete</button>
		<a href="{{ route('group-school.index') }}">Cancel</a>
	</form>
</div>
