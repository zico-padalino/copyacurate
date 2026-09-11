<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = ['code', 'name', 'type', 'opening_balance'];

    protected function casts(): array
    {
        return ['opening_balance' => 'decimal:2'];
    }

    public function debitEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'debit_account_id');
    }

    public function creditEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'credit_account_id');
    }

    public function balance(): float
    {
        $debits = (float) $this->debitEntries()->sum('amount');
        $credits = (float) $this->creditEntries()->sum('amount');
        $opening = (float) $this->opening_balance;

        return match ($this->type) {
            'asset', 'expense' => $opening + $debits - $credits,
            default => $opening + $credits - $debits,
        };
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'asset' => 'Aset',
            'liability' => 'Liabilitas',
            'equity' => 'Ekuitas',
            'revenue' => 'Pendapatan',
            'expense' => 'Beban',
            default => $this->type,
        };
    }
}
