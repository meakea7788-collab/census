<div class="container">
	<h1>{{ __('Delete Group School') }}</h1>

	<p>{{ __('Are you sure you want to delete the group school') }}
		<strong>{{ $groupSchool->name ?? __('this group school') }}</strong>?
		{{ __('This action cannot be undone.') }}
	</p>

	<form action="{{ route('group-school.destroy', $groupSchool->id) }}" method="POST">
		@csrf
		@method('DELETE')

		<button type="submit">{{ __('Delete') }}</button>
		<a href="{{ route('group-school.index') }}">{{ __('Cancel') }}</a>
	</form>
</div>
