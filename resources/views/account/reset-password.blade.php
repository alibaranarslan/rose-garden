@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-12">
    <h1 class="text-2xl font-display text-rg-darkPlum mb-6 text-center">Şifre Sıfırla</h1>

    <div class="bg-white rounded-card border border-rg-lightLavender p-8">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-rg-darkText mb-1">E-posta</label>
                <input type="email" name="email" id="email" value="{{ old('email', $email) }}" required
                       class="w-full px-4 py-2 border border-rg-lightLavender rounded-lg focus:ring-2 focus:ring-rg-purple focus:border-rg-purple">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-rg-darkText mb-1">Yeni Şifre</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-2 border border-rg-lightLavender rounded-lg focus:ring-2 focus:ring-rg-purple focus:border-rg-purple">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-rg-darkText mb-1">Şifre Tekrar</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full px-4 py-2 border border-rg-lightLavender rounded-lg focus:ring-2 focus:ring-rg-purple focus:border-rg-purple">
            </div>

            <button type="submit"
                    class="w-full bg-rg-purple hover:bg-rg-darkPlum text-white font-semibold py-2 px-4 rounded-lg transition">
                Şifreyi Sıfırla
            </button>
        </form>
    </div>
</div>
@endsection
