<?php

namespace App\Exports;

use App\Models\Pembelian;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CustomerExport implements FromArray, WithHeadings, WithCustomStartCell, WithEvents
{
    protected $data = [];

    public function __construct()
    {
        $this->generateData();
    }

    public function generateData()
    {
        $transactions = Pembelian::with(['customer', 'details.produk'])->get();

        foreach ($transactions as $transaction) {
            $isFirst = true;

            foreach ($transaction->details as $detail) {
                $hargaPerItem = $detail->sub_total / $detail->quantity;

                $this->data[] = [
                    $isFirst ? ($transaction->customer->id ?? '') : '',
                    $isFirst ? ($transaction->customer->nama ?? 'Bukan Member') : '',
                    $isFirst ? ($transaction->customer->no_hp ?? '-') : '',
                    $isFirst ? ($transaction->customer->total_point ?? 0) : '',
                    $detail->produk->nama_produk,
                    $detail->quantity,
                    'Rp ' . number_format($hargaPerItem, 2, ',', '.'),
                    $isFirst ? 'Rp ' . number_format($transaction->total_price, 2, ',', '.') : '',
                    $isFirst ? 'Rp ' . number_format($transaction->total_payment, 2, ',', '.') : '',
                    $isFirst ? 'Rp ' . number_format($transaction->used_point, 2, ',', '.') : '',
                    $isFirst ? 'Rp ' . number_format($transaction->total_return, 2, ',', '.') : '',
                    $isFirst ? $transaction->created_at->format('d-m-Y') : '',
                ];

                $isFirst = false;
            }
        }
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID Pelanggan',
            'Nama Pelanggan',
            'No HP Pelanggan',
            'Poin Pelanggan',
            'Produk',
            'QTY',
            'Harga per 1',
            'Total Harga',
            'Total Bayar',
            'Total Diskon Poin',
            'Total Kembalian',
            'Tanggal Pembelian',
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'Data Penjualan');
                $sheet->mergeCells('A1:L1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                foreach (range('A', 'L') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
