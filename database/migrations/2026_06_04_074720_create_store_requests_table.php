<?php

use App\Models\StoreSubscription;
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
        Schema::create('store_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(StoreSubscription::class)->constrained()
                ->cascadeOnDelete();

            $table->string('customer_full_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_contact');

            $table->string('quantity');

            $table->enum('status', [
                'pending', // En attente
                'processing', // En cours
                'available', // Disponible
                'cancelled', // Annulée
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
        Schema::dropIfExists('store_requests');
    }
};
