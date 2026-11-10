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
        Schema::create('customer_demos', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('demo_link')->nullable();

            $table->boolean('is_active')->default(true);

            $table->text('admin_note')->nullable();

            $table->string('image')->nullable();

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_demos');
    }
};
