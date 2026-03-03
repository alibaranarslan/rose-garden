@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-12">
    <h1 class="text-2xl font-display text-rg-darkPlum mb-6 text-center">Şifremi Unuttum</h1>

    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-card mb-6">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white rounded-card border border-rg-lightLavender p-8">
        <p class="text-sm text-rg-grayText mb-6">
            E-posta adresinizi girin, size şifre sıfırlama bağlantısı gönderelim.
        </p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-rg-darkText mb-1">E-posta</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2 border border-rg-lightLavender rounded-lg focus:ring-2 focus:ring-rg-purple focus:border-rg-purple">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                    class="w-full bg-rg-purple hover:bg-rg-darkPlum text-white font-semibold py-2 px-4 rounded-lg transition">
                Sıfırlama Bağlantısı Gönder
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-rg-purple hover:underline">Giriş sayfasına dön</a>
        </div>
    </div>
</div>
@endsection
