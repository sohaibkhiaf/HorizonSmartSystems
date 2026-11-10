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
        Schema::create('public_heroes', function (Blueprint $table) {
            $table->id();

            $table->string('ar_label')->nullable();
            $table->string('fr_label')->nullable();
            $table->string('en_label')->nullable();

            $table->string('ar_title');
            $table->string('fr_title');
            $table->string('en_title');

            $table->text('ar_description')->nullable();
            $table->text('fr_description')->nullable();
            $table->text('en_description')->nullable();

            $table->string('image')->nullable();

            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('whatsapp_url')->nullable();
            $table->string('linked_in_url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_heroes');
    }
};
