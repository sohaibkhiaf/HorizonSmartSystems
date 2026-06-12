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
        Schema::create('public_achievements', function (Blueprint $table) {
            $table->id();

            $table->string('ar_title');
            $table->string('fr_title');
            $table->string('en_title');

            $table->string('ar_type');
            $table->string('fr_type');
            $table->string('en_type');

            $table->string('ar_short_description');
            $table->string('fr_short_description');
            $table->string('en_short_description');

            $table->text('ar_description')->nullable();
            $table->text('fr_description')->nullable();
            $table->text('en_description')->nullable();


            $table->string('image')->nullable();
            $table->string('project_url')->nullable();

            $table->boolean('is_published')->default(true);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_achievements');
    }
};
