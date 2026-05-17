@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'autofocus' => false,
    'autocomplete' => null,
])

@php
    $base = 'w-full rounded-xl border border-rg-lightLavender bg-white px-4 py-3 text-sm text-rg-darkText shadow-sm outline-none transition focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/45 dark:border-white/15 dark:bg-rg-deepPurple/40 dark:text-white dark:placeholder:text-white/40 dark:focus:border-rg-lavender dark:focus:ring-rg-lavender/35';
@endphp

<div>
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-rg-darkText dark:text-white/90">{{ $label }}</label>
    <input
        id="{{ $name }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @required($required)
        @if($autofocus) autofocus @endif
        {{ $attributes->class([
            $base,
            'border-red-400 ring-2 ring-red-200/50 dark:border-red-400/60' => $errors->has($name),
        ]) }}
    >
    @error($name)
        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
