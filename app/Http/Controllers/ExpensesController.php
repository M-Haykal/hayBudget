<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'nama_item' => 'required|string|max:100',
            'kategori'  => 'nullable|string|max:50',
            'jumlah'    => 'required|integer|min:1',
            'tanggal'   => 'required|date',
            'catatan'   => 'nullable|string',
        ]);

        Expense::create($request->all());

        return back()->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function destroy(Expense $expense)
    {
        $budget = $expense->budget;
        $expense->delete();

        return redirect()->route('budget.show', [
            'year'  => $budget->year,
            'month' => $budget->month,
        ])->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
