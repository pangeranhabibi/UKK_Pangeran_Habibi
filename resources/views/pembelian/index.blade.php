@extends('layouts.master')

@section('title', 'Home Page')

@section('content')
<style>
    .search{
        margin-left: 30px;
    }
</style>
<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 d-flex align-items-center">
                        <li class="breadcrumb-item"><a href="index.html" class="link"><i
                                    class="mdi mdi-home-outline fs-4"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Penjualan</li>
                    </ol>
                </nav>
                <h1 class="mb-0 fw-bold">Penjualan</h1>
            </div>
        </div>
    </div>
 
    <div class="search">
        <form method="GET" action="{{ route('pembelian.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="cari" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>
    </div>

    <div class="container-fluid">
<div class="row">
<div class="col-12">
<div class="card">
    <div class="card-body">
        <div class="row justify-content-end mb-3">
            <div class="col text-start">
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('pembelian.exportExcel')}}" class="btn btn-info">
                            Export Penjualan (.xlsx)
                        </a>
                    </div>
                </div>
            </div>
            @if (Auth::user()->role == 'employe')
            <div class="col text-end">
                <a href="{{ route('pembelian.create')}}" class="btn btn-primary">
                    Tambah Penjualan
                </a>
            </div>
            @endif
        </div>
        <div class="table-responsive">
            <table id="salesTable" class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nama Pelanggan</th>
                        <th scope="col">Tanggal Penjualan</th>
                        <th scope="col">Total Harga</th>
                        <th scope="col">Dibuat Oleh</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale as $item)
                        <tr>
                            <th>{{ $sale->firstItem() + $loop->index }}</th>
                            <td>{{ $item->customer ? $item->customer->nama : 'NON-MEMBER' }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                            <td>Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                            <td>{{ $item->user->nama }}</td>
                            <td>
                                <div class="d-flex justify-content-around">
                                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#lihat-{{ $item->id }}">Lihat</button>
                                    <form action="{{ route('pembelian.export.pdf', $item->id)}}" method="post">
                                        @csrf
                                        <button type="submit" class="btn btn-info">Unduh Bukti</button>
                                     </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <!-- Modal untuk setiap transaksi -->
                @foreach($sale as $item)
                <div class="modal fade" id="lihat-{{ $item->id }}" tabindex="-1" aria-labelledby="modalLihat" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLihat">Detail Penjualan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6">
                                        <small>
                                            <p>Member Status : {{ $item->customer ? 'Member' : 'NON-MEMBER' }}</p>
                                            <p>No. HP : {{ $item->customer->no_hp ?? '-' }}</p>
                                            <p>Poin Member : {{ $item->customer->total_point ?? '-' }}</p>
                                        </small>
                                    </div>
                                    <div class="col-6">
                                        <small>
                                            Bergabung Sejak : {{ $item->customer ? date('d F Y', strtotime($item->customer->created_at)) : '-' }}
                                        </small>
                                    </div>
                                </div>
                                {{-- <div class="row mb-3 text-center mt-5">
                                    <tr>
                                        <th><div class="col-3"><b>Nama Produk</b></div></th>
                                        <th><div class="col-3"><b>Qty</b></div></th>
                                        <th><div class="col-3"><b>Harga</b></div></th>
                                        <th><div class="col-3"><b>Sub Total</b></div></th>
                                    </tr>   
                                </div>
                                <div class="row mb-1">
                                    @foreach ($item->details as $detail)
                                        <tr>
                                            <td>{{ $detail->produk->nama_produk }}</td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>Rp.{{ number_format($detail->produk->harga, 0, ',', '.') }}</td>
                                            <td>Rp.{{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </div> --}} 

                                <table class="table table-bordered mt-4">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Nama Produk</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @foreach ($item->details as $detail)
                                            <tr>
                                                <td>{{ $detail->produk->nama_produk }}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>Rp.{{ number_format($detail->produk->harga, 0, ',', '.') }}</td>
                                                <td>Rp.{{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                
                                <div class="row text-center mt-3">
                                    <div class="col-9 text-end"><b>Total</b></div>
                                    <div class="col-3"><b>Rp.{{ number_format($item->total_price, 0, ',', '.') }}</b></div>
                                </div>
                                <div class="row mt-3">
                                    <center>
                                        Dibuat pada : {{ \Carbon\Carbon::parse($item->created_at)->timezone('Asia/Jakarta')->format('d M Y H:i:s') }}  <br> 
                                        Oleh : {{ $item->user->nama }}
                                    </center>
                                    
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div> 
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
     {{-- Pagination --}}
   {{-- Pagination --}}
   <<div class="mt-4 d-flex justify-content-between align-items-center flex-wrap">
    <div class="text-muted small">
        Menampilkan {{ $sale->firstItem() }} sampai {{ $sale->lastItem() }} dari total {{ $sale->total() }} entri
    </div>
    <div class="mt-2 mt-md-0">
        {{ $sale->withQueryString()->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>
</div>
</div>
</div>
    </div>
</div>
@endsection