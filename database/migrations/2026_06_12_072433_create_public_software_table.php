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
        Schema::create('public_software', function (Blueprint $table) {
            $table->id();

            $table->string('ar_name');
            $table->string('fr_name');
            $table->string('en_name');

            $table->string('ar_short_description');
            $table->string('fr_short_description');
            $table->string('en_short_description');
            
            $table->text('ar_description');
            $table->text('fr_description');
            $table->text('en_description');

            $table->string('image')->nullable();
            $table->string('icon')->nullable();

            $table->boolean('is_published')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_software');
    }
};
