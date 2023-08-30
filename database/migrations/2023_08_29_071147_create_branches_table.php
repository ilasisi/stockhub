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
        Schema::create('branches', function (Blueprint $table): void {
            $table->uuid('id');
            $table->string('name');
            $table->string('slug');
            $table->text('address');
            $table->text('contact_phone');
            $table->timestamps();
        });

        Schema::create('branch_user', function (Blueprint $table): void {
            $table->id();
            $table->uuid('user_id')->constrained()->cascadeOnDelete()->index();
            $table->uuid('branch_id')->constrained()->cascadeOnDelete()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
        Schema::dropIfExists('branch_user');
    }
};
