<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;

class BudgetController extends Controller
{
    public function index()
    {
        $now = now();
        return redirect()->route('budget.show', [
            'year'  => $now->year,
            'month' => $now->month,
        ]);
    }

    public function show(int $year, int $month)
    {
        // Semua budget untuk render tab, urutkan terbaru duluan
        $allBudgets = Budget::orderByDesc('year')
                            ->orderByDesc('month')
                            ->get();

        // Budget bulan yang dipilih, auto-create kalau belum ada
        $budget = Budget::firstOrCreate(
            ['month' => $month, 'year' => $year],
            ['dana'  => 0]
        );

        $expenses = $budget->expenses()->orderBy('tanggal')->get();
        $incomes  = $budget->incomes()->orderBy('tanggal')->get();

        $totalPemasukan   = $incomes->sum('jumlah');
        $totalMasuk       = $budget->dana + $totalPemasukan;
        $totalPengeluaran = $expenses->sum('jumlah');
        $sisaDana         = $totalMasuk - $totalPengeluaran;

        return view('budget.show', compact(
            'allBudgets',
            'budget',
            'expenses',
            'incomes',
            'totalPemasukan',
            'totalMasuk',
            'totalPengeluaran',
            'sisaDana'
        ));
    }

    public function updateDana(Request $request, Budget $budget)
    {
        $request->validate([
            'dana' => 'required|integer|min:0',
        ]);

        $budget->update(['dana' => $request->dana]);

        return back()->with('success', 'Dana dari orang tua berhasil diupdate.');
    }

    public function exportPdf(int $year, int $month)
    {
        $budget = Budget::where('month', $month)
                        ->where('year', $year)
                        ->firstOrFail();

        $expenses = $budget->expenses()->orderBy('tanggal')->get();
        $incomes  = $budget->incomes()->orderBy('tanggal')->get();

        $totalPemasukan   = $incomes->sum('jumlah');
        $totalMasuk       = $budget->dana + $totalPemasukan;
        $totalPengeluaran = $expenses->sum('jumlah');
        $sisaDana         = $totalMasuk - $totalPengeluaran;

        $pdf = Pdf::loadView('budget.pdf', compact(
            'budget',
            'expenses',
            'incomes',
            'totalPemasukan',
            'totalMasuk',
            'totalPengeluaran',
            'sisaDana'
        ))->setPaper('a4', 'portrait');

        $filename = 'rekap-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }
}
