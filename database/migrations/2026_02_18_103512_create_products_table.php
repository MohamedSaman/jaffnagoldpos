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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code');
            $table->string('name');
            $table->foreignId('category_id')->constrained();
            $table->foreignId('purity_id')->constrained();
            $table->decimal('gross_weight', 8, 3)->nullable();
            $table->decimal('stone_weight', 8, 3)->nullable();
            $table->decimal('net_weight', 8, 3)->nullable();
            $table->enum('making_charge_type', ['fixed', 'per_gram']);
            $table->decimal('making_charge', 10, 2);
            $table->decimal('wastage_percentage', 5, 2)->nullable();
            $table->integer('stock_quantity')->default(1);
            $table->string('barcode')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
