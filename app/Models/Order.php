<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $product_id
 * @property int|null $voucher_id
 * @property string $customer_name
 * @property string $customer_phone
 * @property string $customer_address
 * @property int $quantity
 * @property float $discount_amount
 * @property float $total_amount
 * @property string|null $notes
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['product_id', 'voucher_id', 'customer_name', 'customer_phone', 'customer_address', 'quantity', 'discount_amount', 'total_amount', 'notes', 'status'])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const STATUSES = ['new', 'processing', 'shipped', 'completed', 'cancelled'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<Voucher, $this>
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Build a wa.me link to contact the customer, assuming a Malaysian phone number.
     */
    public function whatsappUrl(): string
    {
        $digits = preg_replace('/\D/', '', $this->customer_phone);

        if (str_starts_with($digits, '0')) {
            $digits = '6'.$digits;
        } elseif (! str_starts_with($digits, '60')) {
            $digits = '60'.$digits;
        }

        $message = __('Hi :name, mengenai pesanan anda untuk :product.', [
            'name' => $this->customer_name,
            'product' => $this->product->name,
        ]);

        return 'https://wa.me/'.$digits.'?text='.rawurlencode($message);
    }
}
