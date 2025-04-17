<?php

namespace App\Exports;

use App\Models\Pembelian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;

class CustomerExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
{
    public function collection()
    {
        return Pembelian::with(['customer', 'details.produk'])->get();
    }

    public function headings(): array
    {
        return [
            'Nama Pelanggan',
            'No HP Pelanggan',
            'Poin Pelanggan',
            'Produk',
            'Total Harga',
            'Total Bayar',
            'Total Diskon Poin',
            'Total Kembalian',
            'Tanggal Pembelian'
        ];
    }

    public function map($transaction): array
    {
        $products = $transaction->details->map(function ($detail) {
            return $detail->produk->nama_produk . "\n(" . $detail->quantity . ' x Rp ' . number_format($detail->sub_total, 2, ',', '.') . ')';
        })->implode("\n");

        return [
            $transaction->customer->nama ?? 'Bukan Member',
            $transaction->customer->no_hp ?? '-',
            $transaction->customer->total_point ?? 0,
            $products,
            'Rp ' . number_format($transaction->total_price, 2, ',', '.'),
            'Rp ' . number_format($transaction->total_payment, 2, ',', '.'),
            'Rp ' . number_format($transaction->used_point, 2, ',', '.'),
            'Rp ' . number_format($transaction->total_return, 2, ',', '.'),
            $transaction->created_at->format('d-m-Y'),
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Judul
                $sheet->setCellValue('A1', 'Data Penjualan');
                $sheet->mergeCells('A1:I1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // AutoSize kolom biar menyesuaikan isi
                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Wrap Text dan auto-height hanya di kolom produk (D)
                $highestRow = $sheet->getHighestRow();
                for ($row = 3; $row <= $highestRow; $row++) {
                    $sheet->getStyle("D{$row}")->getAlignment()->setWrapText(true);
                    $sheet->getRowDimension($row)->setRowHeight(-1); // -1 = auto height
                }
            },
        ];
    }
}
