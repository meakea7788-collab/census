<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Welcome') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        @if (app()->getLocale() === 'km')
            <style>body { font-family: "Noto Sans Khmer", "Khmer OS System", sans-serif; }</style>
        @endif
    </head>
    <body class="bg-slate-100 min-h-screen flex items-center justify-center">
        <div class="w-full max-w-5xl rounded-3xl bg-white shadow-2xl overflow-hidden border border-slate-200">
            <form method="POST" action="{{ route('language.switch') }}" class="absolute right-6 top-6">
                @csrf
                <select name="locale" class="rounded-lg border border-slate-300 bg-white px-3 py-2" onchange="this.form.submit()" aria-label="{{ __('Language') }}">
                    <option value="km" @selected(app()->getLocale() === 'km')>ភាសាខ្មែរ</option>
                    <option value="en" @selected(app()->getLocale() === 'en')>English</option>
                </select>
            </form>
            <div class="grid md:grid-cols-2">
                <div class="p-10 md:p-16 flex flex-col justify-center bg-gradient-to-br from-indigo-600 to-blue-500 text-white">
                    <p class="text-sm uppercase tracking-[0.2em] text-indigo-100">{{ __('Census System') }}</p>
                    <h1 class="mt-5 text-4xl md:text-5xl font-bold leading-tight">{{ __('Welcome') }}</h1>
                    <p class="mt-5 text-lg text-indigo-100">
                        {{ __('Manage your census records with a clean and simple dashboard experience.') }}
                    </p>
                    <div class="mt-8">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 font-semibold text-indigo-700 shadow hover:bg-slate-100 transition">
                            {{ __('Login') }}
                        </a>
                    </div>
                </div>

                <div class="p-10 md:p-16 flex items-center justify-center bg-slate-50">
                    <div class="w-full max-w-sm">
                        <div class="mx-auto mb-8 flex h-28 w-28 items-center justify-center overflow-hidden rounded-full border-4 border-slate-300 bg-white p-3 shadow-inner">
                            <img
                                src="{{ asset('images/cambodia-national-emblem.png') }}"
                                alt="{{ __('Cambodia national emblem') }}"
                                class="h-full w-full object-contain"
                            >
                        </div>
                        <div class="space-y-4">
                            <div class="h-4 w-3/4 rounded bg-slate-200"></div>
                            <div class="h-4 w-5/6 rounded bg-slate-200"></div>
                            <div class="h-4 w-2/3 rounded bg-slate-200"></div>
                            <div class="h-4 w-4/5 rounded bg-slate-200"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
