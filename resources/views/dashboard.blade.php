@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid p-0">
        <div class="mb-4">
            <h1 class="h3 mb-2">Dashboard</h1>
            <p class="text-secondary mb-0">Welcome to the Census Admin dashboard.</p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 card-title">Quick links</h2>
                <p class="card-text text-secondary">Manage group schools or add a new group school.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('group-school.index') }}" class="btn btn-primary">View Group Schools</a>
                    <a href="{{ route('group-school.create') }}" class="btn btn-outline-primary">Add Group School</a>
                </div>
            </div>
        </div>
    </div>
@endsection
