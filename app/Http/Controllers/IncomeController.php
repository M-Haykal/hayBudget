<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Incomes as Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'sumber'    => 'required|string|max:100',
            'jumlah'    => 'required|integer|min:1',
            'tanggal'   => 'required|date',
            'catatan'   => 'nullable|string',
        ]);

        Income::create($request->all());

        return back()->with('success', 'Pemasukan berhasil ditambahkan.');
    }

    public function destroy(Income $income)
    {
        $budget = $income->budget;
        $income->delete();

        return redirect()->route('budget.show', [
            'year'  => $budget->year,
            'month' => $budget->month,
        ])->with('success', 'Pemasukan berhasil dihapus.');
    }
}
