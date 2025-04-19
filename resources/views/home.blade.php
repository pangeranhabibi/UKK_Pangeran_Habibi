@extends('layouts.master')

@section('title', 'Home Page')

@section('content')
<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 d-flex align-items-center">
                        <li class="breadcrumb-item">
                            <a href="index.html" class="link">
                                <i class="mdi mdi-home-outline fs-4"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
                <h1 class="mb-0 fw-bold">Dashboard</h1>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if (Auth::user()->role == 'admin')
                            <h3>Selamat Datang, Administrator!</h3>

                            <div class="row">
                                <div class="col-md-8 mb-4">
                                    <canvas id="myChart" style="width: 100%; height: 300px;"></canvas>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <canvas id="myChart2" style="width: 100%; height: 300px;"></canvas>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <canvas id="stokChart" style="width: 100%; max-width: 500px; height: 300px; margin: 0 auto;"></canvas>
                                </div>
                            </div>

                            <hr>

                        @else
                            <h3>Selamat Datang, Petugas!</h3>

                            <div class="card d-block m-auto text-center">
                                <div class="card-header">
                                    <h5 class="card-title">Total Penjualan Hari ini</h5>
                                </div>
                                <div class="card-body text-center">
                                    <div class="total-sales">{{ $todaySalesCount }}</div>
                                    <p class="card-text">Jumlah total penjualan yang terjadi hari ini.</p>
                                </div>
                                <div class="card-footer">
                                    <i class="far fa-clock me-2"></i>
                                    Terakhir diperbarui: {{ $lastTransactionTime }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = @json($chartData);
        const labels = chartData.map(item => item.date);
        const counts = chartData.map(item => item.count);

        const ctx1 = document.getElementById('myChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Penjualan',
                    data: counts,
                    backgroundColor: 'rgba(54, 162, 235, 0.4)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Jumlah Penjualan',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        display: false
                    }
                }
            }
        });

        const productLabels = @json($productLabels);
        const productData = @json($productData);

        const ctx2 = document.getElementById('myChart2').getContext('2d');
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: productLabels,
                datasets: [{
                    data: productData,
                    backgroundColor: productLabels.map(() => {
                        const r = Math.floor(Math.random() * 255);
                        const g = Math.floor(Math.random() * 255);
                        const b = Math.floor(Math.random() * 255);
                        return `rgba(${r}, ${g}, ${b}, 0.6)`;
                    }),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Presentasi Penjualan Produk',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });

        const stokLabels = @json($stokLabels);
        const stokData = @json($stokData);

        const ctx3 = document.getElementById('stokChart').getContext('2d');
        new Chart(ctx3, {
            type: 'doughnut',
            data: {
                labels: stokLabels,
                datasets: [{
                    data: stokData,
                    backgroundColor: stokLabels.map(() => {
                        const r = Math.floor(Math.random() * 255);
                        const g = Math.floor(Math.random() * 255);
                        const b = Math.floor(Math.random() * 255);
                        return `rgba(${r}, ${g}, ${b}, 0.6)`;
                    }),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Presentasi Stok Produk',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    });
</script>
@endsection
