<?php

use App\Livewire\Forms\ProductForm;
use App\Models\Product;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Edit Produk')] class extends Component {
    use WithFileUploads;

    public Product $product;

    public ProductForm $form;

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->form->setFromProduct($product);

        if (empty($this->form->benefits)) {
            $this->form->addBenefit();
        }

        if (empty($this->form->testimonials)) {
            $this->form->addTestimonial();
        }
    }

    public function save(): void
    {
        $this->product->update($this->form->dataForSave());

        Flux::toast(variant: 'success', text: __('Produk dikemaskini.'));
    }
}; ?>

<section class="w-full max-w-2xl">
    <flux:heading size="xl">{{ __('Edit Produk') }}</flux:heading>

    <form wire:submit="save" class="mt-6 space-y-6">
        @include('pages.admin.products.partials.form-fields')

        <flux:button variant="primary" type="submit">{{ __('Kemaskini Produk') }}</flux:button>
    </form>
</section>
