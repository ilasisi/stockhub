<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table): void {
            $table->uuid('id');
            $table->string('ref');
            $table->uuid('product_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->double('unit_price', 8, 2);
            $table->double('total_price', 8, 2);
            $table->double('grand_total', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
