<?php

use App\Livewire\Forms\VoucherForm;
use App\Models\Product;
use App\Models\Voucher;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit Voucher')] class extends Component {
    public Voucher $voucher;

    public VoucherForm $form;

    public function mount(Voucher $voucher): void
    {
        $this->voucher = $voucher;
        $this->form->setFromVoucher($voucher);
    }

    public function save(): void
    {
        $this->voucher->update($this->form->dataForSave());
        $this->voucher->products()->sync($this->form->productIds);

        Flux::toast(variant: 'success', text: __('Voucher dikemaskini.'));
    }

    #[Computed]
    public function products(): Collection
    {
        return Product::orderBy('name')->get(['id', 'name']);
    }
}; ?>

<section class="w-full max-w-2xl">
    <flux:heading size="xl">{{ __('Edit Voucher') }}</flux:heading>

    <form wire:submit="save" class="mt-6 space-y-6">
        @include('pages.admin.vouchers.partials.form-fields', ['products' => $this->products])

        <flux:button variant="primary" type="submit">{{ __('Kemaskini Voucher') }}</flux:button>
    </form>
</section>
