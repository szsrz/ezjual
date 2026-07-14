<?php

use App\Livewire\Forms\ProductForm;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Tambah Produk')] class extends Component {
    use WithFileUploads;

    public ProductForm $form;

    public function mount(): void
    {
        $this->form->addBenefit();
        $this->form->addTestimonial();
    }

    public function save(): void
    {
        Product::create($this->form->dataForSave());

        $this->redirect(route('products.index'), navigate: true);
    }
}; ?>

<section class="w-full max-w-2xl">
    <flux:heading size="xl">{{ __('Tambah Produk') }}</flux:heading>

    <form wire:submit="save" class="mt-6 space-y-6">
        @include('pages.admin.products.partials.form-fields')

        <flux:button variant="primary" type="submit">{{ __('Simpan Produk') }}</flux:button>
    </form>
</section>
