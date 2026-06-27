<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expenses';

    protected $fillable = [
        'budget_id',
        'nama_item',
        'kategori',
        'jumlah',
        'tanggal',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
