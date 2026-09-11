<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = ['type', 'name', 'email', 'phone', 'tax_number', 'address'];

    public function salesInvoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class);
    }

    public function purchaseBills(): HasMany
    {
        return $this->hasMany(PurchaseBill::class);
    }

    public function isCustomer(): bool
    {
        return in_array($this->type, ['customer', 'both'], true);
    }

    public function isVendor(): bool
    {
        return in_array($this->type, ['vendor', 'both'], true);
    }
}
