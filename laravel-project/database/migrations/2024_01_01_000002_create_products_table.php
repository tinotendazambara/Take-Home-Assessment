<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // BUG: No check constraint to prevent negative prices
            $table->integer('stock')->default(0); // BUG: No check constraint to prevent negative stock
            $table->string('category')->nullable();
            $table->timestamps();
            
            // BUG: Missing index on category for faster filtering
            // BUG: Missing index on price for sorting
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

