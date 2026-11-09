@extends('layouts.app')

@section('content')
    <form action="{{ route('years.store') }}" method="POST">
        @csrf

        <div>
            <label for="year">{{ __('Year') }}</label>
            <select id="year" name="year" required>
                <option value="">{{ __('Select year') }}</option>
                @for ($year = 2000; $year <= 2050; $year++)
                    <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endfor
            </select>
            @error('year')
                <div>{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">{{ __('Create Year') }}</button>
    </form>
@endsection
