<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashAccount extends Model
{
    protected $fillable = ['name', 'type', 'account_number', 'balance'];

    protected function casts(): array
    {
        return ['balance' => 'decimal:2'];
    }

    public function adjustBalance(float $amount): void
    {
        $this->balance = (float) $this->balance + $amount;
        $this->save();
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'cash' => 'Kas',
            'bank' => 'Bank',
            'e_wallet' => 'E-Wallet',
            default => $this->type,
        };
    }
}
