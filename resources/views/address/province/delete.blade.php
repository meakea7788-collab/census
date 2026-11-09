<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ __('Delete Province') }}</title>
</head>
<body>
	<main>
		<h1>{{ __('Delete Province') }}</h1>
		<p>{{ __('Are you sure you want to delete this province?') }}</p>

		<dl>
			<dt>{{ __('Name') }}</dt>
			<dd>{{ $province->name }}</dd>
		</dl>

		<form action="{{ route('address.province.destroy', $province->id) }}" method="POST">
			@csrf
			@method('DELETE')
			<button type="submit">{{ __('Delete Province') }}</button>
			<a href="{{ route('address.province.index') }}">{{ __('Cancel') }}</a>
		</form>
	</main>
</body>
</html>
