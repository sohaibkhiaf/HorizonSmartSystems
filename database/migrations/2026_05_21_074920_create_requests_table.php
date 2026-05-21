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
            $table->string('full_name');
            $table->string('company_name')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('service_type');
            $table->string('project_title')->nullable();
            $table->longText('project_description')->nullable();
            $table->boolean('visual_identity')->nullable();
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
