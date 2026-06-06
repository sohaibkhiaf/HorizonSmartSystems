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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('operator_id')->constrained('users');
            $table->string('client_name');
            $table->string('client_contact')->nullable();

            $table->enum('status', [
                'pending',
                'processing',
                'available',
                'not_available',
                'paid',
                'delivered'
            ])->default('pending');

            $table->text('delivery_credentials')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
