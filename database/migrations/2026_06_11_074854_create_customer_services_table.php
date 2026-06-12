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
        Schema::create('customer_services', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)->constrained()
                ->cascadeOnDelete();
                
            $table->string('name');
            $table->date('expires_at')->nullable();
            $table->enum('status' , [
                'active', //Actif
                'expired',  // Expiré
                'maintenance', // Maintenance
                'suspended', // Suspendu
            ])->default('active');

            $table->string('license_key')->nullable();
            $table->string('download_link')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_services');
    }
};
