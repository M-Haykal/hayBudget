<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\BudgetController;

Route::get('/', [BudgetController::class, 'index']);

// Budget
Route::get('/rekap/{year}/{month}',         [BudgetController::class, 'show'])->name('budget.show');
Route::patch('/rekap/{budget}/dana',        [BudgetController::class, 'updateDana'])->name('budget.updateDana');
Route::get('/rekap/{year}/{month}/pdf',     [BudgetController::class, 'exportPdf'])->name('budget.pdf');

// Incomes
Route::post('/incomes',                     [IncomeController::class, 'store'])->name('incomes.store');
Route::delete('/incomes/{income}',          [IncomeController::class, 'destroy'])->name('incomes.destroy');

// Expenses
Route::post('/expenses',                    [ExpensesController::class, 'store'])->name('expenses.store');
Route::delete('/expenses/{expense}',        [ExpensesController::class, 'destroy'])->name('expenses.destroy');
