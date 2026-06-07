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
            $table->foreignId('product_id')->nullable()->constrained('products')
                ->onDelete('set null');
            $table->foreignId('operator_id')->nullable()->constrained('users')
                ->onDelete('set null');
            $table->string('quantity');
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_address')->nullable();
            $table->string('client_contact');

            $table->enum('status', [
                'pending', // En attente
                'processing', // En cours
                'available', // Disponible
                'canceled', // Annulée
                'postponed', // Reportée
                'paid', // Payée
                'delivered' // Livrée
            ])->default('pending'); 

            $table->text('admin_note')->nullable();

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
