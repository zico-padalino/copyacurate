<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesInvoice extends Model
{
    protected $fillable = ['contact_id', 'number', 'invoice_date', 'due_date', 'status', 'subtotal', 'tax_amount', 'total', 'paid_amount'];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function outstandingAmount(): float
    {
        return max(0, (float) $this->total - (float) $this->paid_amount);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['sent', 'partial', 'overdue'], true);
    }

    /**
     * @param  Builder<SalesInvoice>  $query
     * @return Builder<SalesInvoice>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', ['sent', 'partial', 'overdue']);
    }

    public function applyPayment(float $amount): void
    {
        $paid = min((float) $this->total, (float) $this->paid_amount + $amount);
        $this->paid_amount = $paid;

        if ($paid >= (float) $this->total) {
            $this->status = 'paid';
        } elseif ($paid > 0) {
            $this->status = 'partial';
        }

        $this->save();
    }

    public static function nextNumber(): string
    {
        $sequence = static::query()->count() + 1;

        return 'INV-'.now()->format('Y').'-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
