<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VoucherManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get(route('vouchers.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_percentage_voucher_for_specific_products(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create();

        Livewire::test('pages::admin.vouchers.create')
            ->set('form.code', 'raya10')
            ->set('form.type', 'percentage')
            ->set('form.value', '10')
            ->set('form.productIds', [$product->id])
            ->call('save')
            ->assertRedirect(route('vouchers.index'));

        $voucher = Voucher::sole();

        $this->assertSame('RAYA10', $voucher->code);
        $this->assertSame('percentage', $voucher->type);
        $this->assertTrue($voucher->products->contains($product));
    }

    public function test_authenticated_user_can_edit_a_voucher_and_toggle_active_state(): void
    {
        $this->actingAs(User::factory()->create());
        $voucher = Voucher::factory()->create(['code' => 'OLD10']);
        $product = Product::factory()->create();

        Livewire::test('pages::admin.vouchers.edit', ['voucher' => $voucher])
            ->set('form.code', 'NEW10')
            ->set('form.productIds', [$product->id])
            ->call('save');

        $this->assertSame('NEW10', $voucher->fresh()->code);
        $this->assertTrue($voucher->fresh()->products->contains($product));

        Livewire::test('pages::admin.vouchers.index')
            ->call('toggleActive', $voucher->id);

        $this->assertFalse($voucher->fresh()->is_active);
    }

    public function test_voucher_code_must_be_unique(): void
    {
        $this->actingAs(User::factory()->create());
        Voucher::factory()->create(['code' => 'DUPE10']);
        $product = Product::factory()->create();

        Livewire::test('pages::admin.vouchers.create')
            ->set('form.code', 'DUPE10')
            ->set('form.type', 'percentage')
            ->set('form.value', '10')
            ->set('form.productIds', [$product->id])
            ->call('save')
            ->assertHasErrors(['form.code' => 'unique']);
    }
}
