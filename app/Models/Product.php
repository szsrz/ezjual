<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $tagline
 * @property string|null $description
 * @property array<int, string>|null $benefits
 * @property array<int, array{name: string, content: string}>|null $testimonials
 * @property string|null $image
 * @property float $price
 * @property float|null $compare_at_price
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'tagline', 'description', 'benefits', 'testimonials', 'image', 'price', 'compare_at_price', 'status'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'testimonials' => 'array',
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return BelongsToMany<Voucher, $this>
     */
    public function vouchers(): BelongsToMany
    {
        return $this->belongsToMany(Voucher::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (blank($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}
