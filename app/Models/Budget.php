<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    protected $table = 'budgets';

    protected $fillable = ['month', 'year', 'dana'];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'budget_id');
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class, 'budget_id');
    }

    public function getMonthNameAttribute(): string
    {
        return Carbon::create($this->year, $this->month)
                     ->translatedFormat('F Y');
    }
}
