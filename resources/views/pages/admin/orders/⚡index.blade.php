<?php

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Pesanan')] class extends Component {
    #[Url]
    public string $status = '';

    #[Url]
    public string $productId = '';

    public function updateStatus(Order $order, string $status): void
    {
        $order->update(['status' => $status]);
    }

    public function resetFilters(): void
    {
        $this->reset('status', 'productId');
    }

    #[Computed]
    public function orders(): Collection
    {
        return Order::query()
            ->with(['product', 'voucher'])
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->productId, fn ($query) => $query->where('product_id', $this->productId))
            ->latest()
            ->get();
    }

    #[Computed]
    public function productOptions(): Collection
    {
        return Product::orderBy('name')->get(['id', 'name']);
    }
}; ?>

<section class="w-full">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Pesanan') }}</flux:heading>
            <flux:subheading>{{ __('Urus pesanan yang masuk daripada pelanggan') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('orders.create')" wire:navigate>
            {{ __('Tambah Pesanan') }}
        </flux:button>
    </div>

    <div class="mt-6 flex gap-4">
        <flux:select wire:model.live="status" :placeholder="__('Semua Status')" class="max-w-xs">
            <flux:select.option value="">{{ __('Semua Status') }}</flux:select.option>
            @foreach (\App\Models\Order::STATUSES as $value)
                <flux:select.option value="{{ $value }}">{{ ucfirst($value) }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="productId" :placeholder="__('Semua Produk')" class="max-w-xs">
            <flux:select.option value="">{{ __('Semua Produk') }}</flux:select.option>
            @foreach ($this->productOptions as $product)
                <flux:select.option value="{{ $product->id }}">{{ $product->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:button wire:click="resetFilters" variant="ghost" icon="x-mark">
            {{ __('Reset') }}
        </flux:button>
    </div>

    <flux:table class="mt-6">
        <flux:table.columns>
            <flux:table.column>{{ __('Pelanggan') }}</flux:table.column>
            <flux:table.column>{{ __('Produk') }}</flux:table.column>
            <flux:table.column>{{ __('Kuantiti') }}</flux:table.column>
            <flux:table.column>{{ __('Jumlah') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Tarikh') }}</flux:table.column>
            <flux:table.column>{{ __('Tindakan') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->orders as $order)
                <flux:table.row wire:key="order-{{ $order->id }}">
                    <flux:table.cell>
                        <div>{{ $order->customer_name }}</div>
                        <flux:text size="sm" class="opacity-70">{{ $order->customer_phone }}</flux:text>
                    </flux:table.cell>
                    <flux:table.cell>{{ $order->product->name }}</flux:table.cell>
                    <flux:table.cell>{{ $order->quantity }}</flux:table.cell>
                    <flux:table.cell>
                        <div>RM {{ number_format($order->total_amount, 2) }}</div>
                        @if ($order->voucher)
                            <flux:text size="sm" class="opacity-70">{{ __('Voucher: :code', ['code' => $order->voucher->code]) }}</flux:text>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:select wire:change="updateStatus({{ $order->id }}, $event.target.value)" size="sm">
                            @foreach (\App\Models\Order::STATUSES as $value)
                                <flux:select.option value="{{ $value }}" :selected="$order->status === $value">
                                    {{ ucfirst($value) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:table.cell>
                    <flux:table.cell>{{ $order->created_at->format('d/m/Y H:i') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button size="sm" icon="phone" :href="$order->whatsappUrl()" target="_blank">
                            {{ __('WhatsApp') }}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">
                        <flux:text class="py-6 text-center">{{ __('Tiada pesanan lagi.') }}</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
