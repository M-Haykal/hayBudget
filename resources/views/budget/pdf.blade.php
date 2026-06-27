<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            padding: 32px;
        }

        h1 {
            font-size: 18px;
            font-weight: 700;
            color: #1e40af;
        }

        .subtitle {
            color: #64748b;
            font-size: 10px;
            margin-top: 2px;
        }

        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 16px 0;
        }

        .summary {
            display: flex;
            gap: 0;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }

        .summary-item {
            flex: 1;
            padding: 12px 14px;
            border-right: 1px solid #e2e8f0;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-label {
            font-size: 9px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .summary-value {
            font-size: 13px;
            font-weight: 700;
            margin-top: 3px;
        }

        .blue {
            color: #2563eb;
        }

        .red {
            color: #ef4444;
        }

        .green {
            color: #10b981;
        }

        .gray {
            color: #475569;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        th {
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 10.5px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .section-title {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
            margin-top: 20px;
        }

        .text-right {
            text-align: right;
        }

        .mono {
            font-family: 'DejaVu Sans Mono', monospace;
        }
    </style>
</head>

<body>
    <h1>eBudget — Rekap Bulanan</h1>
    <p class="subtitle">{{ $budget->month_name }} · Dicetak {{ now()->translatedFormat('d F Y, H:i') }}</p>

    <hr class="divider">

    <div class="summary">
        <div class="summary-item">
            <p class="summary-label">Dana Orang Tua</p>
            <p class="summary-value gray mono">Rp {{ number_format($budget->dana, 0, ',', '.') }}</p>
        </div>
        <div class="summary-item">
            <p class="summary-label">Pemasukan Kerja</p>
            <p class="summary-value blue mono">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
        </div>
        <div class="summary-item">
            <p class="summary-label">Total Pengeluaran</p>
            <p class="summary-value red mono">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
        </div>
        <div class="summary-item">
            <p class="summary-label">Sisa Dana</p>
            <p class="summary-value {{ $sisaDana >= 0 ? 'green' : 'red' }} mono">Rp
                {{ number_format($sisaDana, 0, ',', '.') }}</p>
        </div>
    </div>

    @if ($incomes->isNotEmpty())
        <p class="section-title">Pemasukan Kerja</p>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Sumber</th>
                    <th>Catatan</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($incomes as $income)
                    <tr>
                        <td>{{ $income->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $income->sumber }}</td>
                        <td>{{ $income->catatan ?? '-' }}</td>
                        <td class="text-right mono blue">Rp {{ number_format($income->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($expenses->isNotEmpty())
        <p class="section-title">Pengeluaran</p>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Item</th>
                    <th>Kategori</th>
                    <th>Catatan</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenses as $expense)
                    <tr>
                        <td>{{ $expense->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $expense->nama_item }}</td>
                        <td>{{ $expense->kategori ?? '-' }}</td>
                        <td>{{ $expense->catatan ?? '-' }}</td>
                        <td class="text-right mono red">Rp {{ number_format($expense->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>

</html>
