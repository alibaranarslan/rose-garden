<div x-data="{ qty: 1 }" class="inline-flex items-center border border-rg-lightLavender rounded-btn overflow-hidden">
    <button type="button" class="px-3 py-2" @click="qty = Math.max(1, qty - 1)">-</button>
    <span class="px-3 py-2 text-sm" x-text="qty"></span>
    <button type="button" class="px-3 py-2" @click="qty++">+</button>
</div>
