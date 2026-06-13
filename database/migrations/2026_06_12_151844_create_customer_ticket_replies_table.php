<?php

use App\Models\CustomerTicket;
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
        Schema::create('customer_ticket_replies', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(CustomerTicket::class)->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(User::class)->constrained()
                ->cascadeOnDelete();

            $table->enum('author_type', [
                'customer', 
                'admin',
            ])->default('customer');

            $table->text('message');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_ticket_replies');
    }
};
