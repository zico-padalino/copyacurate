<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    protected $fillable = ['asset_code', 'name', 'acquired_at', 'acquisition_cost', 'useful_life_months', 'accumulated_depreciation', 'status'];

    protected function casts(): array
    {
        return [
            'acquired_at' => 'date',
            'acquisition_cost' => 'decimal:2',
            'accumulated_depreciation' => 'decimal:2',
        ];
    }

    public function bookValue(): float
    {
        return max(0, (float) $this->acquisition_cost - (float) $this->accumulated_depreciation);
    }

    public function monthlyDepreciation(): float
    {
        if ($this->useful_life_months <= 0) {
            return 0;
        }

        return round((float) $this->acquisition_cost / $this->useful_life_months, 2);
    }

    public function postMonthlyDepreciation(): void
    {
        if ($this->status !== 'active') {
            return;
        }

        $amount = min($this->monthlyDepreciation(), $this->bookValue());
        $this->accumulated_depreciation = (float) $this->accumulated_depreciation + $amount;
        $this->save();
    }

    public function dispose(): void
    {
        $this->status = 'disposed';
        $this->save();
    }
}
