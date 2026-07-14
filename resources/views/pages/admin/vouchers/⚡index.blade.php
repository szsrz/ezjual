<?php

use App\Models\Voucher;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Voucher')] class extends Component {
    public function toggleActive(Voucher $voucher): void
    {
        $voucher->update(['is_active' => ! $voucher->is_active]);
    }

    public function delete(Voucher $voucher): void
    {
        $voucher->delete();

        Flux::toast(variant: 'success', text: __('Voucher dipadam.'));
    }

    #[Computed]
    public function vouchers(): Collection
    {
        return Voucher::with('products')->latest()->get();
    }
}; ?>

<section class="w-full">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Voucher') }}</flux:heading>
            <flux:subheading>{{ __('Urus kod diskaun untuk produk anda') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('vouchers.create')" wire:navigate>
            {{ __('Tambah Voucher') }}
        </flux:button>
    </div>

    <flux:table class="mt-6">
        <flux:table.columns>
            <flux:table.column>{{ __('Kod') }}</flux:table.column>
            <flux:table.column>{{ __('Diskaun') }}</flux:table.column>
            <flux:table.column>{{ __('Produk') }}</flux:table.column>
            <flux:table.column>{{ __('Luput') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Tindakan') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->vouchers as $voucher)
                <flux:table.row wire:key="voucher-{{ $voucher->id }}">
                    <flux:table.cell>{{ $voucher->code }}</flux:table.cell>
                    <flux:table.cell>
                        {{ $voucher->type === 'percentage' ? $voucher->value.'%' : 'RM '.number_format($voucher->value, 2) }}
                    </flux:table.cell>
                    <flux:table.cell>{{ $voucher->products->pluck('name')->join(', ') }}</flux:table.cell>
                    <flux:table.cell>{{ $voucher->expires_at?->format('d/m/Y') ?? __('Tiada') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="$voucher->isValid() ? 'green' : 'zinc'" size="sm">
                            {{ $voucher->isValid() ? __('Aktif') : __('Tidak Aktif') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <flux:link :href="route('vouchers.edit', $voucher)" wire:navigate>{{ __('Edit') }}</flux:link>
                            <flux:link wire:click="toggleActive({{ $voucher->id }})" class="cursor-pointer">
                                {{ $voucher->is_active ? __('Nyahaktifkan') : __('Aktifkan') }}
                            </flux:link>
                            <flux:link
                                wire:click="delete({{ $voucher->id }})"
                                wire:confirm="{{ __('Padam voucher ini?') }}"
                                class="cursor-pointer !text-red-600"
                            >
                                {{ __('Padam') }}
                            </flux:link>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">
                        <flux:text class="py-6 text-center">{{ __('Tiada voucher lagi.') }}</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
