<?php

use App\Livewire\Forms\VoucherForm;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tambah Voucher')] class extends Component {
    public VoucherForm $form;

    public function save(): void
    {
        $voucher = Voucher::create($this->form->dataForSave());
        $voucher->products()->sync($this->form->productIds);

        $this->redirect(route('vouchers.index'), navigate: true);
    }

    #[Computed]
    public function products(): Collection
    {
        return Product::orderBy('name')->get(['id', 'name']);
    }
}; ?>

<section class="w-full max-w-2xl">
    <flux:heading size="xl">{{ __('Tambah Voucher') }}</flux:heading>

    <form wire:submit="save" class="mt-6 space-y-6">
        @include('pages.admin.vouchers.partials.form-fields', ['products' => $this->products])

        <flux:button variant="primary" type="submit">{{ __('Simpan Voucher') }}</flux:button>
    </form>
</section>
