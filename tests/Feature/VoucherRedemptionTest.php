<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VoucherRedemptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_apply_a_valid_percentage_voucher(): void
    {
        $product = Product::factory()->published()->create(['price' => 100]);
        $voucher = Voucher::factory()->create(['code' => 'DISKAUN10', 'type' => 'percentage', 'value' => 10]);
        $voucher->products()->attach($product);

        Livewire::test('pages::products.show', ['product' => $product])
            ->set('voucher_code', 'diskaun10')
            ->call('applyVoucher')
            ->assertHasNoErrors()
            ->assertSet('appliedVoucher.code', 'DISKAUN10');
    }

    public function test_customer_cannot_apply_a_voucher_for_a_different_product(): void
    {
        $product = Product::factory()->published()->create();
        $otherProduct = Product::factory()->create();
        $voucher = Voucher::factory()->create(['code' => 'OTHER10']);
        $voucher->products()->attach($otherProduct);

        Livewire::test('pages::products.show', ['product' => $product])
            ->set('voucher_code', 'OTHER10')
            ->call('applyVoucher')
            ->assertHasErrors('voucher_code')
            ->assertSet('appliedVoucher', null);
    }

    public function test_customer_cannot_apply_an_expired_voucher(): void
    {
        $product = Product::factory()->published()->create();
        $voucher = Voucher::factory()->expired()->create(['code' => 'EXPIRED10']);
        $voucher->products()->attach($product);

        Livewire::test('pages::products.show', ['product' => $product])
            ->set('voucher_code', 'EXPIRED10')
            ->call('applyVoucher')
            ->assertHasErrors('voucher_code');
    }

    public function test_order_total_reflects_fixed_voucher_discount(): void
    {
        $product = Product::factory()->published()->create(['price' => 50]);
        $voucher = Voucher::factory()->fixed(15)->create(['code' => 'FIXED15']);
        $voucher->products()->attach($product);

        Livewire::test('pages::products.show', ['product' => $product])
            ->set('customer_name', 'Ali')
            ->set('customer_phone', '0123456789')
            ->set('customer_address', 'Alamat Test')
            ->set('quantity', 2)
            ->set('voucher_code', 'FIXED15')
            ->call('applyVoucher')
            ->call('placeOrder');

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Ali',
            'discount_amount' => '15.00',
            'total_amount' => '85.00',
        ]);
    }
}
