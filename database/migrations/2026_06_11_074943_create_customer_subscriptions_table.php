<?php

use App\Models\User;
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
        Schema::create('customer_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->integer('price');
            $table->date('renewal_date');

            $table->enum('status', [
                'active', // Actif
                'pending', //En attente
                'cancelled', // Annulé
                'expired', // Expiré
            ])->default('active');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_subscriptions');
    }
};
