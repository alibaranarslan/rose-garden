@props(['count' => 5])
<div class="text-rg-lavender text-sm">
    @for ($i = 0; $i < $count; $i++)
        <span>★</span>
    @endfor
</div>
