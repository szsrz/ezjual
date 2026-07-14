<?php

namespace App\Livewire\Forms;

use App\Models\Voucher;
use Illuminate\Validation\Rule;
use Livewire\Form;

class VoucherForm extends Form
{
    public string $code = '';

    public string $type = 'percentage';

    public string $value = '';

    public string $expires_at = '';

    public bool $is_active = true;

    /**
     * @var array<int, int>
     */
    public array $productIds = [];

    private ?int $ignoreVoucherId = null;

    public function setFromVoucher(Voucher $voucher): void
    {
        $this->ignoreVoucherId = $voucher->id;
        $this->code = $voucher->code;
        $this->type = $voucher->type;
        $this->value = (string) $voucher->value;
        $this->expires_at = $voucher->expires_at?->format('Y-m-d') ?? '';
        $this->is_active = $voucher->is_active;
        $this->productIds = $voucher->products()->pluck('products.id')->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function dataForSave(): array
    {
        $this->code = strtoupper($this->code);

        $this->validate([
            'code' => [
                'required', 'string', 'max:50',
                Rule::unique('vouchers', 'code')->ignore($this->ignoreVoucherId),
            ],
            'type' => ['required', 'in:'.implode(',', Voucher::TYPES)],
            'value' => ['required', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['boolean'],
            'productIds' => ['required', 'array', 'min:1'],
            'productIds.*' => ['integer', 'exists:products,id'],
        ]);

        return [
            'code' => $this->code,
            'type' => $this->type,
            'value' => $this->value,
            'expires_at' => $this->expires_at ?: null,
            'is_active' => $this->is_active,
        ];
    }
}
