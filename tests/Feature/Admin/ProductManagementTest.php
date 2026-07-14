<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get(route('products.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_product_with_an_image(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        Livewire::test('pages::admin.products.create')
            ->set('form.name', 'Kopi Radix')
            ->set('form.price', '39.90')
            ->set('form.newImage', UploadedFile::fake()->image('product.jpg'))
            ->call('save')
            ->assertRedirect(route('products.index'));

        $product = Product::sole();

        $this->assertSame('Kopi Radix', $product->name);
        $this->assertSame('kopi-radix', $product->slug);
        $this->assertSame('draft', $product->status);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_authenticated_user_can_edit_and_publish_a_product(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create(['name' => 'Old Name']);

        Livewire::test('pages::admin.products.edit', ['product' => $product])
            ->set('form.name', 'New Name')
            ->call('save');

        $this->assertSame('New Name', $product->fresh()->name);

        Livewire::test('pages::admin.products.index')
            ->call('togglePublish', $product->id);

        $this->assertSame('published', $product->fresh()->status);
    }
}
