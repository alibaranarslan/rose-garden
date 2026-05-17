@php
    $kvkkEmail = config('mail.from.address', 'info@adiyamancicekcisi.com.tr');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.kvkk_consent_title') }} - Rose Garden</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-gray-50 px-4">

    <div class="my-8 w-full max-w-2xl rounded-2xl bg-white p-8 shadow-lg">

        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('auth.kvkk_consent_title') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('auth.kvkk_consent_subtitle') }}</p>
        </div>

        <div class="prose prose-sm mb-6 max-w-none max-h-64 overflow-y-auto rounded-lg bg-gray-50 p-4 text-sm leading-relaxed text-gray-700">
            <h3 class="mb-2 font-semibold text-gray-900">{{ __('auth.kvkk_notice_heading') }}</h3>

            <p>{{ __('auth.kvkk_notice_intro') }}</p>

            <p><strong>{{ __('auth.kvkk_processed_data_label') }}</strong> {{ __('auth.kvkk_processed_data_text') }}</p>

            <p><strong>{{ __('auth.kvkk_purposes_label') }}</strong></p>
            <ul>
                <li>{{ __('auth.kvkk_purpose_orders') }}</li>
                <li>{{ __('auth.kvkk_purpose_legal') }}</li>
                <li>{{ __('auth.kvkk_purpose_satisfaction') }}</li>
                <li>{{ __('auth.kvkk_purpose_marketing') }}</li>
            </ul>

            <p><strong>{{ __('auth.kvkk_security_label') }}</strong> {{ __('auth.kvkk_security_text') }}</p>

            <p><strong>{{ __('auth.kvkk_rights_label') }}</strong> {!! __('auth.kvkk_rights_text', ['email' => e($kvkkEmail)]) !!}</p>
        </div>

        <form method="POST" action="{{ \App\Support\StorefrontLocale::route('kvkk.consent.store') }}">
            @csrf

            @if(session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @error('kvkk_accepted')
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    {{ $message }}
                </div>
            @enderror

            <label class="mb-6 flex cursor-pointer items-start gap-3">
                <input type="checkbox" name="kvkk_accepted" value="1"
                       class="mt-1 rounded border-gray-300 text-rose-600 focus:ring-rose-500"
                       id="kvkk_accepted">
                <span class="text-sm text-gray-700">
                    <strong>{{ __('auth.kvkk_consent_checkbox_label') }}</strong> {{ __('auth.kvkk_consent_checkbox_text') }}
                </span>
            </label>

            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit"
                        class="flex-1 rounded-lg bg-rose-600 px-6 py-3 font-medium text-white transition-colors hover:bg-rose-700">
                    {{ __('auth.kvkk_accept_continue') }}
                </button>

                <a href="{{ \App\Support\StorefrontLocale::route('kvkk.consent.reject') }}"
                   class="flex-1 rounded-lg border border-gray-300 px-6 py-3 text-center font-medium text-gray-700 transition-colors hover:bg-gray-50">
                    {{ __('auth.kvkk_reject_action') }}
                </a>
            </div>
        </form>
    </div>

</body>
</html>
