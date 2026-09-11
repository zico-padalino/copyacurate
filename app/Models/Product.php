<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['sku', 'name', 'type', 'selling_price', 'buying_price', 'stock', 'minimum_stock', 'is_active'];

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'buying_price' => 'decimal:2',
            'stock' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function isLowStock(): bool
    {
        return (float) $this->stock <= (float) $this->minimum_stock;
    }

    public function adjustStock(float $quantity): void
    {
        $this->stock = max(0, (float) $this->stock + $quantity);
        $this->save();
    }
}
