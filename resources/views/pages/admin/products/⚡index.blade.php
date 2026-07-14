<?php

use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Produk')] class extends Component {
    public function togglePublish(Product $product): void
    {
        $product->update([
            'status' => $product->isPublished() ? 'draft' : 'published',
        ]);
    }

    public function delete(Product $product): void
    {
        $product->delete();

        Flux::toast(variant: 'success', text: __('Produk dipadam.'));
    }

    #[Computed]
    public function products(): Collection
    {
        return Product::withCount('orders')->latest()->get();
    }
}; ?>

<section class="w-full">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Produk') }}</flux:heading>
            <flux:subheading>{{ __('Urus landing page produk anda') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('products.create')" wire:navigate>
            {{ __('Tambah Produk') }}
        </flux:button>
    </div>

    <flux:table class="mt-6">
        <flux:table.columns>
            <flux:table.column>{{ __('Nama') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Harga') }}</flux:table.column>
            <flux:table.column>{{ __('Pesanan') }}</flux:table.column>
            <flux:table.column>{{ __('Tindakan') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->products as $product)
                <flux:table.row wire:key="product-{{ $product->id }}">
                    <flux:table.cell>{{ $product->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="$product->isPublished() ? 'green' : 'zinc'" size="sm">
                            {{ $product->isPublished() ? __('Diterbitkan') : __('Draf') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>RM {{ number_format($product->price, 2) }}</flux:table.cell>
                    <flux:table.cell>{{ $product->orders_count }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <flux:link :href="route('products.show', $product)" target="_blank">{{ __('Lihat') }}</flux:link>
                            <flux:link :href="route('products.edit', $product)" wire:navigate>{{ __('Edit') }}</flux:link>
                            <flux:link wire:click="togglePublish({{ $product->id }})" class="cursor-pointer">
                                {{ $product->isPublished() ? __('Nyahterbit') : __('Terbitkan') }}
                            </flux:link>
                            <flux:link
                                wire:click="delete({{ $product->id }})"
                                wire:confirm="{{ __('Padam produk ini?') }}"
                                class="cursor-pointer !text-red-600"
                            >
                                {{ __('Padam') }}
                            </flux:link>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">
                        <flux:text class="py-6 text-center">{{ __('Tiada produk lagi.') }}</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
