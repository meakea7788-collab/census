@extends('layouts.app')

@section('content')
    <a href="{{ route('years.create') }}" class="btn btn-primary">{{ __('Create Year') }}</a>

    <table>
        <thead>
            <tr>
                <th>{{ __('Year') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($years as $year)
                <tr>
                    <td>{{ $year->year ?? $year }}</td>
                </tr>
            @empty
                <tr>
                    <td>{{ __('No years found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
