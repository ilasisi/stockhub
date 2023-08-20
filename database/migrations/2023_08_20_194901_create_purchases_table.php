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
        Schema::create('purchases', function (Blueprint $table): void {
            $table->uuid('id');
            $table->uuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('payment_type_id')->nullable()->constrained()->nullOnDelete();
            $table->double('items_total', 8, 2);
            $table->double('discount_amount', 8, 2)->nullable();
            $table->double('discount_percentage', 8, 2)->nullable();
            $table->double('vat_amount', 8, 2)->nullable();
            $table->double('vat_percentage', 8, 2)->nullable();
            $table->double('grand_total', 8, 2);
            $table->double('amount_tender', 8, 2);
            $table->double('change_due', 8, 2);
            $table->string('status')->default('processed');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
