<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get(route('orders.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_sees_orders_list(): void
    {
        $this->actingAs(User::factory()->create());
        $order = Order::factory()->create();

        $response = $this->get(route('orders.index'));

        $response->assertOk();
        $response->assertSee($order->customer_name);
    }

    public function test_authenticated_user_can_update_order_status(): void
    {
        $this->actingAs(User::factory()->create());
        $order = Order::factory()->create(['status' => 'new']);

        Livewire::test('pages::admin.orders.index')
            ->call('updateStatus', $order->id, 'processing');

        $this->assertSame('processing', $order->fresh()->status);
    }

    public function test_guests_are_redirected_from_order_create_page(): void
    {
        $this->get(route('orders.create'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_manually_create_an_order(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create(['price' => 40]);

        Livewire::test('pages::admin.orders.create')
            ->set('product_id', $product->id)
            ->set('customer_name', 'Siti')
            ->set('customer_phone', '0123456789')
            ->set('customer_address', 'Alamat Test')
            ->set('quantity', 3)
            ->set('status', 'completed')
            ->call('save')
            ->assertRedirect(route('orders.index'));

        $this->assertDatabaseHas('orders', [
            'product_id' => $product->id,
            'customer_name' => 'Siti',
            'quantity' => 3,
            'status' => 'completed',
            'total_amount' => '120.00',
        ]);
    }

    public function test_admin_created_order_can_apply_an_applicable_voucher(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create(['price' => 100]);
        $voucher = Voucher::factory()->create(['type' => 'percentage', 'value' => 20]);
        $voucher->products()->attach($product);

        Livewire::test('pages::admin.orders.create')
            ->set('product_id', $product->id)
            ->set('voucher_id', $voucher->id)
            ->set('customer_name', 'Ahmad')
            ->set('customer_phone', '0123456789')
            ->set('customer_address', 'Alamat Test')
            ->set('quantity', 1)
            ->call('save');

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Ahmad',
            'voucher_id' => $voucher->id,
            'discount_amount' => '20.00',
            'total_amount' => '80.00',
        ]);
    }
}
