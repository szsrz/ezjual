<?php

use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;
use App\Notifications\NewOrderReceived;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::public')] class extends Component {
    public Product $product;

    public string $customer_name = '';

    public string $customer_phone = '';

    public string $customer_address = '';

    public int $quantity = 1;

    public string $notes = '';

    public string $voucher_code = '';

    public ?Voucher $appliedVoucher = null;

    public bool $orderSubmitted = false;

    public function mount(Product $product): void
    {
        abort_unless($product->isPublished(), 404);

        $this->product = $product;
    }

    public function applyVoucher(): void
    {
        $this->resetErrorBag('voucher_code');

        $voucher = Voucher::where('code', strtoupper(trim($this->voucher_code)))->first();

        if (! $voucher || ! $voucher->isValid() || ! $voucher->appliesToProduct($this->product)) {
            $this->appliedVoucher = null;
            $this->addError('voucher_code', __('Kod voucher tidak sah atau tidak terpakai untuk produk ini.'));

            return;
        }

        $this->appliedVoucher = $voucher;
    }

    public function removeVoucher(): void
    {
        $this->appliedVoucher = null;
        $this->voucher_code = '';
    }

    public function placeOrder(): void
    {
        $validated = $this->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_address' => ['required', 'string', 'max:1000'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $voucher = $this->appliedVoucher
            && $this->appliedVoucher->isValid()
            && $this->appliedVoucher->appliesToProduct($this->product)
                ? $this->appliedVoucher
                : null;

        $order = $this->product->orders()->create([
            ...$validated,
            'voucher_id' => $voucher?->id,
            'discount_amount' => $this->discountAmount,
            'total_amount' => $this->total,
        ]);

        Notification::send(User::all(), new NewOrderReceived($order));

        $this->orderSubmitted = true;
    }

    #[Computed]
    public function imageUrl(): ?string
    {
        return $this->product->image ? Storage::url($this->product->image) : null;
    }

    #[Computed]
    public function subtotal(): float
    {
        return (float) $this->product->price * $this->quantity;
    }

    #[Computed]
    public function discountAmount(): float
    {
        return $this->appliedVoucher ? $this->appliedVoucher->calculateDiscount($this->subtotal) : 0.0;
    }

    #[Computed]
    public function total(): float
    {
        return $this->subtotal - $this->discountAmount;
    }
}; ?>

<div class="mx-auto max-w-3xl px-4 py-10">
    @if ($this->imageUrl)
        <img src="{{ $this->imageUrl }}" alt="{{ $product->name }}" class="mb-6 w-full rounded-xl object-cover" />
    @endif

    <flux:heading size="xl">{{ $product->name }}</flux:heading>

    @if ($product->tagline)
        <flux:subheading size="lg" class="mt-1">{{ $product->tagline }}</flux:subheading>
    @endif

    <div class="mt-4 flex items-baseline gap-3">
        <flux:heading size="lg">RM {{ number_format($product->price, 2) }}</flux:heading>

        @if ($product->compare_at_price)
            <flux:text class="line-through opacity-60">RM {{ number_format($product->compare_at_price, 2) }}</flux:text>
        @endif
    </div>

    @if ($product->description)
        <flux:text class="mt-6 whitespace-pre-line">{{ $product->description }}</flux:text>
    @endif

    @if (! empty($product->benefits))
        <ul class="mt-6 list-disc space-y-2 ps-5">
            @foreach ($product->benefits as $benefit)
                <li><flux:text>{{ $benefit }}</flux:text></li>
            @endforeach
        </ul>
    @endif

    @if (! empty($product->testimonials))
        <div class="mt-8 space-y-4">
            @foreach ($product->testimonials as $testimonial)
                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <flux:text class="italic">&ldquo;{{ $testimonial['content'] }}&rdquo;</flux:text>
                    <flux:text class="mt-2 font-medium">{{ $testimonial['name'] }}</flux:text>
                </div>
            @endforeach
        </div>
    @endif

    <flux:separator class="my-8" />

    @if ($orderSubmitted)
        <div class="rounded-lg border border-green-200 bg-green-50 p-6 text-center dark:border-green-800 dark:bg-green-950">
            <flux:heading>{{ __('Terima kasih! Pesanan anda telah diterima.') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Kami akan hubungi anda tidak lama lagi untuk pengesahan.') }}</flux:text>
        </div>
    @else
        <form wire:submit="placeOrder" class="space-y-4">
            <flux:heading size="lg">{{ __('Buat Pesanan') }}</flux:heading>

            <flux:input wire:model="customer_name" :label="__('Nama Penuh')" required autocomplete="name" />
            <flux:input wire:model="customer_phone" :label="__('No. Telefon')" required autocomplete="tel" />
            <flux:textarea wire:model="customer_address" :label="__('Alamat Penghantaran')" required rows="3" />
            <flux:input wire:model="quantity" :label="__('Kuantiti')" type="number" min="1" required />
            <flux:textarea wire:model="notes" :label="__('Nota (pilihan)')" rows="2" />

            <div>
                <flux:label>{{ __('Kod Voucher (pilihan)') }}</flux:label>

                @if ($appliedVoucher)
                    <div class="mt-2 flex items-center justify-between rounded-lg border border-green-200 bg-green-50 px-3 py-2 dark:border-green-800 dark:bg-green-950">
                        <flux:text>{{ __('Voucher :code diguna pakai.', ['code' => $appliedVoucher->code]) }}</flux:text>
                        <flux:link wire:click="removeVoucher" class="cursor-pointer">{{ __('Buang') }}</flux:link>
                    </div>
                @else
                    <div class="mt-2 flex items-start gap-2">
                        <div class="flex-1">
                            <flux:input wire:model="voucher_code" placeholder="{{ __('Contoh: RAYA10') }}" />
                        </div>
                        <flux:button type="button" wire:click="applyVoucher">{{ __('Guna Kod') }}</flux:button>
                    </div>
                @endif
            </div>

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

            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('Hantar Pesanan') }}
            </flux:button>
        </form>
    @endif
</div>
