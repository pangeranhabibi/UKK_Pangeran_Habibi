<?php

        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;
        
        return new class extends Migration
        {
            /**
                * Run the migrations.
                */
            public function up(): void
            {
                Schema::create('produks', function (Blueprint $table) {
                    $table->id();
                    $table->string('nama_produk');
                    $table->decimal('harga', 15, 2)->unsigned(); // Harga lebih akurat
                    $table->integer('stok')->unsigned(); // Stok tidak bisa negatif
                    $table->string('image', 255)->nullable();
                    $table->timestamps();
                });
            }
        
            /**
                * Reverse the migrations.
                */
            public function down(): void
            {
                Schema::dropIfExists('produks');
            }
        };