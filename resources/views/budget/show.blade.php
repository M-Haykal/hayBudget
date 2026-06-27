@extends('layouts.app')

@section('content')

    {{-- Tab Navigasi Bulan --}}
    <div class="flex items-center gap-1 overflow-x-auto pb-2 mb-6 scrollbar-none">
        @foreach ($allBudgets as $b)
            <a href="{{ route('budget.show', ['year' => $b->year, 'month' => $b->month]) }}"
                class="whitespace-nowrap px-4 py-2 rounded-t-lg text-sm font-medium border transition-all
           {{ $b->id === $budget->id
               ? 'bg-white border-slate-200 border-b-white text-primary shadow-sm -mb-px z-10'
               : 'bg-slate-100 border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-200' }}">
                {{ $b->month_name }}
            </a>
        @endforeach

        {{-- Tombol bulan baru --}}
        <button @click="$dispatch('open-modal', 'new-month')" x-data
            class="whitespace-nowrap px-3 py-2 rounded-t-lg text-sm font-medium text-slate-400 hover:text-primary hover:bg-slate-100 transition-all border border-transparent">
            + Bulan Baru
        </button>
    </div>

    {{-- Panel utama --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Header bulan aktif --}}
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-slate-800">{{ $budget->month_name }}</h1>
                <p class="text-sm text-slate-400 mt-0.5">Rekap keuangan bulan ini</p>
            </div>
            <a href="{{ route('budget.pdf', ['year' => $budget->year, 'month' => $budget->month]) }}"
                class="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm rounded-lg hover:bg-blue-700 transition-colors font-medium">
                ↓ Download PDF
            </a>
        </div>

        {{-- Summary cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-slate-100">
            @php
                $cards = [
                    ['label' => 'Dana Orang Tua', 'value' => $budget->dana, 'color' => 'text-slate-700'],
                    ['label' => 'Pemasukan Kerja', 'value' => $totalPemasukan, 'color' => 'text-blue-600'],
                    ['label' => 'Total Pengeluaran', 'value' => $totalPengeluaran, 'color' => 'text-red-500'],
                    [
                        'label' => 'Sisa Dana',
                        'value' => $sisaDana,
                        'color' => $sisaDana >= 0 ? 'text-emerald-600' : 'text-red-600',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="bg-white px-5 py-4">
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">{{ $card['label'] }}</p>
                    <p class="font-mono text-lg font-semibold mt-1 {{ $card['color'] }}">
                        Rp {{ number_format($card['value'], 0, ',', '.') }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Konten: Pemasukan & Pengeluaran --}}
        <div class="grid md:grid-cols-2 gap-0 divide-x divide-slate-100">

            {{-- Kolom Pemasukan --}}
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Pemasukan Kerja</h2>
                    <button x-data @click="$dispatch('open-modal', 'tambah-pemasukan')"
                        class="text-xs px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium transition-colors">
                        + Tambah
                    </button>
                </div>

                @if ($incomes->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-8">Belum ada pemasukan bulan ini.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($incomes as $income)
                            <div class="flex items-center justify-between py-2.5 px-3 rounded-lg hover:bg-slate-50 group">
                                <div>
                                    <p class="text-sm font-medium text-slate-700">{{ $income->sumber }}</p>
                                    <p class="text-xs text-slate-400">{{ $income->tanggal->format('d M Y') }}
                                        @if ($income->catatan)
                                            · {{ $income->catatan }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-sm font-semibold text-blue-600">
                                        Rp {{ number_format($income->jumlah, 0, ',', '.') }}
                                    </span>
                                    <form method="POST" action="{{ route('incomes.destroy', $income) }}"
                                        onsubmit="return confirm('Hapus pemasukan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-slate-300 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all text-xs">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Kolom Pengeluaran --}}
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Pengeluaran</h2>
                    <button x-data @click="$dispatch('open-modal', 'tambah-pengeluaran')"
                        class="text-xs px-3 py-1.5 bg-red-50 text-red-500 rounded-lg hover:bg-red-100 font-medium transition-colors">
                        + Tambah
                    </button>
                </div>

                @if ($expenses->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-8">Belum ada pengeluaran bulan ini.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($expenses as $expense)
                            <div class="flex items-center justify-between py-2.5 px-3 rounded-lg hover:bg-slate-50 group">
                                <div>
                                    <p class="text-sm font-medium text-slate-700">{{ $expense->nama_item }}</p>
                                    <p class="text-xs text-slate-400">
                                        {{ $expense->tanggal->format('d M Y') }}
                                        @if ($expense->kategori)
                                            · <span
                                                class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-500">{{ $expense->kategori }}</span>
                                        @endif
                                        @if ($expense->catatan)
                                            · {{ $expense->catatan }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-sm font-semibold text-red-500">
                                        Rp {{ number_format($expense->jumlah, 0, ',', '.') }}
                                    </span>
                                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}"
                                        onsubmit="return confirm('Hapus pengeluaran ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-slate-300 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all text-xs">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Footer: update dana orang tua --}}
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
            <p class="text-xs text-slate-400">Dana dari orang tua dapat diubah kapan saja.</p>
            <button x-data @click="$dispatch('open-modal', 'edit-dana')"
                class="text-xs px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-100 transition-colors font-medium">
                Edit Dana Orang Tua
            </button>
        </div>
    </div>


    {{-- ===== MODALS ===== --}}
    <div x-data="modalManager()" @open-modal.window="open($event.detail)" @keydown.escape.window="close()">

        {{-- Backdrop --}}
        <div x-show="activeModal !== null" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="close()" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40">
        </div>

        {{-- Modal: Edit Dana Orang Tua --}}
        <div x-show="activeModal === 'edit-dana'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click.stop class="bg-white rounded-xl shadow-xl w-full max-w-md border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Edit Dana Orang Tua</h3>
                    <button @click="close()" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <form method="POST" action="{{ route('budget.updateDana', $budget) }}" class="p-6 space-y-4">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah Dana (Rp)</label>
                        <input type="number" name="dana" value="{{ $budget->dana }}" min="0"
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary font-mono"
                            placeholder="0">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="close()"
                            class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-lg text-sm hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg text-sm hover:bg-blue-700 transition-colors font-medium">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: Tambah Pemasukan --}}
        <div x-show="activeModal === 'tambah-pemasukan'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click.stop class="bg-white rounded-xl shadow-xl w-full max-w-md border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Tambah Pemasukan</h3>
                    <button @click="close()" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <form method="POST" action="{{ route('incomes.store') }}" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="budget_id" value="{{ $budget->id }}">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Sumber</label>
                        <input type="text" name="sumber" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            placeholder="Driver, Freelance, dll">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" min="1" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary font-mono"
                            placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan <span
                                class="text-slate-400 font-normal">(opsional)</span></label>
                        <input type="text" name="catatan"
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            placeholder="Proyek website, order GoFood, dll">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="close()"
                            class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-lg text-sm hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors font-medium">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: Tambah Pengeluaran --}}
        <div x-show="activeModal === 'tambah-pengeluaran'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click.stop class="bg-white rounded-xl shadow-xl w-full max-w-md border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Tambah Pengeluaran</h3>
                    <button @click="close()" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <form method="POST" action="{{ route('expenses.store') }}" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="budget_id" value="{{ $budget->id }}">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Item</label>
                        <input type="text" name="nama_item" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            placeholder="Makan siang, Bensin, dll">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah (Rp)</label>
                            <input type="number" name="jumlah" min="1" required
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary font-mono"
                                placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori <span
                                    class="text-slate-400 font-normal">(opsional)</span></label>
                            <input type="text" name="kategori"
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                placeholder="Makan, Transport">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Catatan <span
                                class="text-slate-400 font-normal">(opsional)</span></label>
                        <input type="text" name="catatan"
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            placeholder="Warung Bu Sari, Pertamina, dll">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="close()"
                            class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-lg text-sm hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600 transition-colors font-medium">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: Bulan Baru --}}
        <div x-show="activeModal === 'new-month'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click.stop class="bg-white rounded-xl shadow-xl w-full max-w-sm border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Buka Bulan Baru</h3>
                    <button @click="close()" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                {{-- Form ini redirect ke bulan yang dipilih, budget auto-create di controller --}}
                <form x-data="{ month: '{{ now()->month }}', year: '{{ now()->year }}' }" @submit.prevent="window.location.href = `/rekap/${year}/${month}`"
                    class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Bulan</label>
                            <select x-model="month"
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}">
                                        {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tahun</label>
                            <input type="number" x-model="year" min="2020" max="2099"
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary font-mono">
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="close()"
                            class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-lg text-sm hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg text-sm hover:bg-blue-700 transition-colors font-medium">
                            Buka
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function modalManager() {
            return {
                activeModal: null,
                open(name) {
                    this.activeModal = name
                },
                close() {
                    this.activeModal = null
                }
            }
        }
    </script>

@endsection
