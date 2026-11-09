<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Login') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        @if (app()->getLocale() === 'km')
            <style>body { font-family: "Noto Sans Khmer", "Khmer OS System", sans-serif; }</style>
        @endif
    </head>
    <body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 sm:p-6">
        <main class="mx-auto grid w-full max-w-6xl grid-cols-1 items-center gap-8 lg:grid-cols-2 lg:gap-12">
        <div class="w-full max-w-md justify-self-center p-8 space-y-6 bg-white rounded-2xl shadow-xl border border-slate-200">
            <div class="text-center">
                <form method="POST" action="{{ route('language.switch') }}" class="mb-3 text-end">
                    @csrf
                    <select name="locale" class="rounded-lg border border-slate-300 px-3 py-2" onchange="this.form.submit()" aria-label="{{ __('Language') }}">
                        <option value="km" @selected(app()->getLocale() === 'km')>ភាសាខ្មែរ</option>
                        <option value="en" @selected(app()->getLocale() === 'en')>English</option>
                    </select>
                </form>
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm7-10V7a4 4 0 10-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="mt-4 text-2xl font-bold text-slate-800">{{ __('Welcome back') }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ __('Sign in to continue') }}</p>
            </div>

            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-700">{{ __('Email') }}</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        value="{{ old('email') }}"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        placeholder="name@example.com"
                    >
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-slate-700">{{ __('Password') }}</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        placeholder="{{ __('Enter your password') }}"
                    >
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        {{ __('Remember me') }}
                    </label>
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Forgot password?') }}</a>
                </div>

                <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    {{ __('Sign in') }}
                </button>
            </form>

            <p class="text-center text-sm text-slate-500">
                {{ __('Don’t have an account?') }}
                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Create one') }}</a>
            </p>
        </div>
        <div class="hidden lg:flex items-center justify-center" role="img" aria-label="Census community illustration">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 520 480" class="w-full max-w-xl" fill="none">
                <defs>
                    <linearGradient id="scene" x1="72" y1="38" x2="452" y2="438" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#E0E7FF"/>
                        <stop offset="1" stop-color="#C7D2FE"/>
                    </linearGradient>
                    <linearGradient id="house" x1="185" y1="132" x2="346" y2="286" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#818CF8"/>
                        <stop offset="1" stop-color="#4F46E5"/>
                    </linearGradient>
                </defs>
                <circle cx="260" cy="240" r="205" fill="url(#scene)"/>
                <circle cx="411" cy="112" r="25" fill="#FBBF24"/>
                <path d="M66 340c40-47 87-70 140-70 54 0 92 32 139 32 39 0 70-18 109-50v125H66v-37Z" fill="#A5B4FC"/>
                <path d="M70 370c47-37 93-47 138-30 47 17 78 15 118-2 37-16 76-17 127 3v54H70v-25Z" fill="#818CF8"/>
                <path d="m161 196 98-78 99 78v103H161V196Z" fill="url(#house)"/>
                <path d="m143 199 116-93 117 93-13 16-104-82-103 82-13-16Z" fill="#3730A3"/>
                <path d="M231 232h57v67h-57v-67Z" fill="#FEF3C7"/>
                <path d="M245 232v67m29-67v67m-43-34h57" stroke="#F59E0B" stroke-width="4"/>
                <rect x="176" y="215" width="38" height="38" rx="5" fill="#E0E7FF"/>
                <path d="M195 216v37m-19-19h38" stroke="#818CF8" stroke-width="3"/>
                <rect x="306" y="215" width="38" height="38" rx="5" fill="#E0E7FF"/>
                <path d="M325 216v37m-19-19h38" stroke="#818CF8" stroke-width="3"/>
                <rect x="365" y="221" width="77" height="112" rx="13" fill="white"/>
                <rect x="379" y="238" width="48" height="7" rx="3.5" fill="#C7D2FE"/>
                <rect x="379" y="256" width="36" height="7" rx="3.5" fill="#C7D2FE"/>
                <circle cx="386" cy="286" r="6" fill="#6366F1"/>
                <path d="M399 286h28" stroke="#A5B4FC" stroke-width="6" stroke-linecap="round"/>
                <circle cx="386" cy="307" r="6" fill="#34D399"/>
                <path d="M399 307h28" stroke="#A5B4FC" stroke-width="6" stroke-linecap="round"/>
                <circle cx="147" cy="292" r="18" fill="#F3C7A5"/>
                <path d="M123 350c2-29 10-44 24-44s22 15 24 44h-48Z" fill="#0F766E"/>
                <path d="M133 276c2-15 25-18 31-4 3 6 1 14-1 18-3-7-8-10-15-10-5 0-10 2-14 6-2-3-2-7-1-10Z" fill="#334155"/>
                <circle cx="331" cy="299" r="17" fill="#D9A783"/>
                <path d="M307 355c2-28 10-42 24-42s22 14 24 42h-48Z" fill="#F97316"/>
                <path d="M315 286c5-14 28-14 32 1 1 4 0 8-2 12-4-7-9-10-15-10-5 0-10 2-14 6-2-3-2-6-1-9Z" fill="#1E293B"/>
                <circle cx="255" cy="319" r="16" fill="#EAB995"/>
                <path d="M233 370c2-26 9-39 22-39s20 13 22 39h-44Z" fill="#DB2777"/>
                <path d="M241 307c3-12 24-14 29-1 2 4 1 9-1 12-4-6-8-8-14-8-5 0-9 2-12 5-2-2-3-5-2-8Z" fill="#475569"/>
                <path d="M102 372h325" stroke="#3730A3" stroke-width="8" stroke-linecap="round" opacity=".18"/>
                <circle cx="112" cy="152" r="8" fill="#34D399"/>
                <circle cx="383" cy="163" r="7" fill="#F472B6"/>
                <path d="m92 222 6 12 13 2-9 9 2 13-12-6-12 6 2-13-9-9 13-2 6-12Z" fill="#FBBF24"/>
                <path d="m432 176 5 10 11 2-8 8 2 11-10-5-10 5 2-11-8-8 11-2 5-10Z" fill="#34D399"/>
            </svg>
        </div>
        </main>
    </body>
</html>
