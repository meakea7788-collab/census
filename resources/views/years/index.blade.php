@extends('layouts.app')

@section('content')
    <a href="{{ route('years.create') }}" class="btn btn-primary">Create Year</a>

    <table>
        <thead>
            <tr>
                <th>Year</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($years as $year)
                <tr>
                    <td>{{ $year->year ?? $year }}</td>
                </tr>
            @empty
                <tr>
                    <td>No years found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
