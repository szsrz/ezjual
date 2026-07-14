<flux:input wire:model="form.code" :label="__('Kod Voucher')" placeholder="{{ __('Contoh: RAYA10') }}" required autofocus />

<div class="grid grid-cols-2 gap-4">
    <flux:select wire:model="form.type" :label="__('Jenis Diskaun')">
        <flux:select.option value="percentage">{{ __('Peratusan (%)') }}</flux:select.option>
        <flux:select.option value="fixed">{{ __('Jumlah Tetap (RM)') }}</flux:select.option>
    </flux:select>

    <flux:input wire:model="form.value" :label="__('Nilai Diskaun')" type="number" step="0.01" min="0" required />
</div>

<flux:input wire:model="form.expires_at" :label="__('Tarikh Luput (pilihan)')" type="date" />

<flux:checkbox wire:model="form.is_active" :label="__('Aktif')" />

<flux:checkbox.group wire:model="form.productIds" :label="__('Produk Berkenaan')">
    @foreach ($products as $product)
        <flux:checkbox value="{{ $product->id }}" :label="$product->name" />
    @endforeach
</flux:checkbox.group>
