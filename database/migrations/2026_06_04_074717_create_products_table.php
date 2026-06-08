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
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price');
            $table->string('image')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')
                ->onDelete('set null');

            $table->enum('type', [
                'key', // Clé
                'account', // Compte
                'invitation', // Invitation
            ])->default('key'); 

            $table->enum('category', [
                'games', // Jeux
                'gift_cards', // Cartes cadeaux
                'software', // Logiciel
                'subscriptions', // Abonnements
                'in_game_currency', // Monnaie virtuelle
                'other', // Autre
            ])->default('other'); 

            $table->softDeletes();
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
