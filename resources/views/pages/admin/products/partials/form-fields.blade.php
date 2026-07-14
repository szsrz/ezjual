<flux:input wire:model="form.name" :label="__('Nama Produk')" required autofocus />
<flux:input wire:model="form.tagline" :label="__('Tagline (pilihan)')" />
<flux:textarea wire:model="form.description" :label="__('Deskripsi')" rows="5" />

<div class="grid grid-cols-2 gap-4">
    <flux:input wire:model="form.price" :label="__('Harga (RM)')" type="number" step="0.01" min="0" required />
    <flux:input wire:model="form.compare_at_price" :label="__('Harga Asal (pilihan)')" type="number" step="0.01" min="0" />
</div>

<flux:input wire:model="form.newImage" :label="__('Gambar Produk')" type="file" accept="image/*" />

@if ($form->existingImage && ! $form->newImage)
    <img src="{{ \Illuminate\Support\Facades\Storage::url($form->existingImage) }}" alt="" class="h-32 rounded-lg object-cover" />
@endif

<div>
    <flux:heading size="sm">{{ __('Kelebihan Produk') }}</flux:heading>

    <div class="mt-2 space-y-2">
        @foreach ($form->benefits as $index => $benefit)
            <div class="flex items-center gap-2">
                <flux:input wire:model="form.benefits.{{ $index }}" placeholder="{{ __('Contoh: Penghantaran percuma') }}" />
                <flux:button size="sm" variant="ghost" icon="x-mark" wire:click="form.removeBenefit({{ $index }})" type="button" />
            </div>
        @endforeach
    </div>

    <flux:button size="sm" variant="ghost" icon="plus" wire:click="form.addBenefit" type="button" class="mt-2">
        {{ __('Tambah Kelebihan') }}
    </flux:button>
</div>

<div>
    <flux:heading size="sm">{{ __('Testimoni') }}</flux:heading>

    <div class="mt-2 space-y-3">
        @foreach ($form->testimonials as $index => $testimonial)
            <div class="flex items-start gap-2">
                <div class="flex-1 space-y-2">
                    <flux:input wire:model="form.testimonials.{{ $index }}.name" :placeholder="__('Nama pelanggan')" />
                    <flux:textarea wire:model="form.testimonials.{{ $index }}.content" :placeholder="__('Ulasan pelanggan')" rows="2" />
                </div>
                <flux:button size="sm" variant="ghost" icon="x-mark" wire:click="form.removeTestimonial({{ $index }})" type="button" />
            </div>
        @endforeach
    </div>

    <flux:button size="sm" variant="ghost" icon="plus" wire:click="form.addTestimonial" type="button" class="mt-2">
        {{ __('Tambah Testimoni') }}
    </flux:button>
</div>
