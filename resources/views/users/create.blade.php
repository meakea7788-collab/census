<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Create User') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-slate-100 min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
            <form method="POST" action="{{ route('language.switch') }}" class="mb-4 text-end">
                @csrf
                <select name="locale" class="rounded-lg border border-slate-300 px-3 py-2" onchange="this.form.submit()" aria-label="{{ __('Language') }}">
                    <option value="km" @selected(app()->getLocale() === 'km')>ភាសាខ្មែរ</option>
                    <option value="en" @selected(app()->getLocale() === 'en')>English</option>
                </select>
            </form>
            <h1 class="text-2xl font-bold text-slate-800 mb-6">{{ __('Create User') }}</h1>

            @if (session('status'))
                <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        placeholder="{{ __('Enter full name') }}"
                    >
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-700">{{ __('Email (optional)') }}</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        placeholder="{{ __('Optional email') }}"
                    >
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-slate-700">{{ __('Password (optional)') }}</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        placeholder="{{ __('Optional password') }}"
                    >
                </div>

                <div>
                    <label for="role" class="mb-2 block text-sm font-medium text-slate-700">{{ __('Role') }}</label>
                    <select
                        id="role"
                        name="role"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                    >
                        <option value="superadmin">{{ __('Superadmin') }}</option>
                        <option value="admin">{{ __('Admin') }}</option>
                        <option value="user" selected>{{ __('Normal User') }}</option>
                    </select>
                </div>

                <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white hover:bg-indigo-500">
                    {{ __('Save User') }}
                </button>
            </form>
        </div>
    </body>
</html>
