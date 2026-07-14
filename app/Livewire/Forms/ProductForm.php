<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;
use Livewire\WithFileUploads;

class ProductForm extends Form
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public string $tagline = '';

    #[Validate('nullable|string')]
    public string $description = '';

    #[Validate('required|numeric|min:0')]
    public string $price = '';

    #[Validate('nullable|numeric|min:0')]
    public string $compare_at_price = '';

    /**
     * @var UploadedFile|TemporaryUploadedFile|null
     */
    #[Validate('nullable|image|max:4096')]
    public $newImage = null;

    public ?string $existingImage = null;

    /**
     * @var array<int, string>
     */
    public array $benefits = [];

    /**
     * @var array<int, array{name: string, content: string}>
     */
    public array $testimonials = [];

    public function setFromProduct(Product $product): void
    {
        $this->name = $product->name;
        $this->tagline = (string) $product->tagline;
        $this->description = (string) $product->description;
        $this->price = (string) $product->price;
        $this->compare_at_price = (string) $product->compare_at_price;
        $this->existingImage = $product->image;
        $this->benefits = $product->benefits ?? [];
        $this->testimonials = $product->testimonials ?? [];
    }

    public function addBenefit(): void
    {
        $this->benefits[] = '';
    }

    public function removeBenefit(int $index): void
    {
        unset($this->benefits[$index]);
        $this->benefits = array_values($this->benefits);
    }

    public function addTestimonial(): void
    {
        $this->testimonials[] = ['name' => '', 'content' => ''];
    }

    public function removeTestimonial(int $index): void
    {
        unset($this->testimonials[$index]);
        $this->testimonials = array_values($this->testimonials);
    }

    /**
     * @return array<string, mixed>
     */
    public function dataForSave(): array
    {
        $this->validate();

        $benefits = array_values(array_filter($this->benefits, fn ($benefit) => trim($benefit) !== ''));
        $testimonials = array_values(array_filter(
            $this->testimonials,
            fn ($testimonial) => trim($testimonial['name']) !== '' && trim($testimonial['content']) !== ''
        ));

        return [
            'name' => $this->name,
            'tagline' => $this->tagline ?: null,
            'description' => $this->description ?: null,
            'price' => $this->price,
            'compare_at_price' => $this->compare_at_price ?: null,
            'benefits' => $benefits,
            'testimonials' => $testimonials,
            'image' => $this->storeImage() ?? $this->existingImage,
        ];
    }

    private function storeImage(): ?string
    {
        if (! $this->newImage instanceof UploadedFile) {
            return null;
        }

        $path = $this->newImage->store('products', 'public');

        return $path === false ? null : $path;
    }
}
