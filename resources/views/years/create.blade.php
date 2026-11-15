@extends('layouts.app')

@section('content')
    <form action="{{ route('years.store') }}" method="POST">
        @csrf

        <div>
            <label for="year">{{ __('Year') }}</label>
            <input id="year" name="year" type="number" min="1900" max="2100" value="{{ old('year') }}" required>
            @error('year')
                <div>{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="description">{{ __('Description') }}</label>
            <input id="description" name="description" type="text" maxlength="255" value="{{ old('description') }}">
            @error('description')
                <div>{{ $message }}</div>
            @enderror
        </div>

        <div>
            <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active'))>
            <label for="is_active">{{ __('Active') }}</label>
        </div>

        <button type="submit">{{ __('Create Year') }}</button>
    </form>
@endsection
