<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ProductLandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_a_published_product(): void
    {
        $product = Product::factory()->published()->create();

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        $response->assertSee($product->name);
    }

    public function test_guest_gets_404_for_a_draft_product(): void
    {
        $product = Product::factory()->create();

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_guest_can_submit_an_order_and_notification_is_sent(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $product = Product::factory()->published()->create();

        Livewire::test('pages::products.show', ['product' => $product])
            ->set('customer_name', 'Ali bin Abu')
            ->set('customer_phone', '0123456789')
            ->set('customer_address', 'No 1, Jalan Test, Kuala Lumpur')
            ->set('quantity', 2)
            ->call('placeOrder')
            ->assertSet('orderSubmitted', true);

        $this->assertDatabaseHas('orders', [
            'product_id' => $product->id,
            'customer_name' => 'Ali bin Abu',
            'quantity' => 2,
        ]);

        Notification::assertSentTo($admin, NewOrderReceived::class);
    }

    public function test_order_submission_requires_valid_fields(): void
    {
        $product = Product::factory()->published()->create();

        Livewire::test('pages::products.show', ['product' => $product])
            ->set('customer_name', '')
            ->call('placeOrder')
            ->assertHasErrors(['customer_name' => 'required']);

        $this->assertDatabaseCount('orders', 0);
    }
}
