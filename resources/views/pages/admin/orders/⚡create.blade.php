<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tambah Pesanan')] class extends Component {
    public ?int $product_id = null;

    public ?int $voucher_id = null;

    public string $customer_name = '';

    public string $customer_phone = '';

    public string $customer_address = '';

    public int $quantity = 1;

    public string $notes = '';

    public string $status = 'new';

    public function updatedProductId(): void
    {
        $this->voucher_id = null;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'product_id' => ['required', 'exists:products,id'],
            'voucher_id' => ['nullable', 'exists:vouchers,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_address' => ['required', 'string', 'max:1000'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:'.implode(',', Order::STATUSES)],
        ]);

        Order::create([
            ...$validated,
            'discount_amount' => $this->discountAmount,
            'total_amount' => $this->total,
        ]);

        $this->redirect(route('orders.index'), navigate: true);
    }

    #[Computed]
    public function products(): Collection
    {
        return Product::orderBy('name')->get(['id', 'name', 'price']);
    }

    #[Computed]
    public function selectedProduct(): ?Product
    {
        return $this->products->firstWhere('id', $this->product_id);
    }

    #[Computed]
    public function availableVouchers(): Collection
    {
        if (! $this->selectedProduct) {
            return collect();
        }

        return $this->selectedProduct->vouchers()
            ->where('is_active', true)
            ->get()
            ->filter(fn (Voucher $voucher) => $voucher->isValid());
    }

    #[Computed]
    public function selectedVoucher(): ?Voucher
    {
        return $this->availableVouchers->firstWhere('id', $this->voucher_id);
    }

    #[Computed]
    public function subtotal(): float
    {
        return $this->selectedProduct ? (float) $this->selectedProduct->price * $this->quantity : 0.0;
    }

    #[Computed]
    public function discountAmount(): float
    {
        return $this->selectedVoucher ? $this->selectedVoucher->calculateDiscount($this->subtotal) : 0.0;
    }

    #[Computed]
    public function total(): float
    {
        return $this->subtotal - $this->discountAmount;
    }
}; ?>

<section class="w-full max-w-2xl">
    <flux:heading size="xl">{{ __('Tambah Pesanan') }}</flux:heading>
    <flux:subheading>{{ __('Cipta pesanan secara manual, contoh untuk pesanan telefon atau walk-in') }}</flux:subheading>

    <form wire:submit="save" class="mt-6 space-y-6">
        <flux:select wire:model.live="product_id" :label="__('Produk')" required>
            <flux:select.option value="">{{ __('Pilih produk') }}</flux:select.option>
            @foreach ($this->products as $product)
                <flux:select.option value="{{ $product->id }}">{{ $product->name }} (RM {{ number_format($product->price, 2) }})</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model="customer_name" :label="__('Nama Pelanggan')" required autofocus />
        <flux:input wire:model="customer_phone" :label="__('No. Telefon')" required />
        <flux:textarea wire:model="customer_address" :label="__('Alamat Penghantaran')" required rows="3" />
        <flux:input wire:model.live="quantity" :label="__('Kuantiti')" type="number" min="1" required />
        <flux:textarea wire:model="notes" :label="__('Nota (pilihan)')" rows="2" />

        @if ($this->availableVouchers->isNotEmpty())
            <flux:select wire:model.live="voucher_id" :label="__('Voucher (pilihan)')">
                <flux:select.option value="">{{ __('Tiada voucher') }}</flux:select.option>
                @foreach ($this->availableVouchers as $voucher)
                    <flux:select.option value="{{ $voucher->id }}">
                        {{ $voucher->code }} ({{ $voucher->type === 'percentage' ? $voucher->value.'%' : 'RM '.number_format($voucher->value, 2) }})
                    </flux:select.option>
                @endforeach
            </flux:select>
        @endif

        <flux:select wire:model="status" :label="__('Status')">
            @foreach (\App\Models\Order::STATUSES as $value)
                <flux:select.option value="{{ $value }}">{{ ucfirst($value) }}</flux:select.option>
            @endforeach
        </flux:select>

        @if ($this->selectedProduct)
            <div class="space-y-1 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <div class="flex justify-between">
                    <flux:text>{{ __('Subjumlah') }}</flux:text>
                    <flux:text>RM {{ number_format($this->subtotal, 2) }}</flux:text>
                </div>

                @if ($this->discountAmount > 0)
                    <div class="flex justify-between">
                        <flux:text>{{ __('Diskaun') }}</flux:text>
                        <flux:text class="text-green-600 dark:text-green-400">- RM {{ number_format($this->discountAmount, 2) }}</flux:text>
                    </div>
                @endif

                <div class="flex justify-between">
                    <flux:heading size="sm">{{ __('Jumlah') }}</flux:heading>
                    <flux:heading size="sm">RM {{ number_format($this->total, 2) }}</flux:heading>
                </div>
            </div>
        @endif

        <flux:button variant="primary" type="submit">{{ __('Simpan Pesanan') }}</flux:button>
    </form>
</section>
