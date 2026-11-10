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
        Schema::table('customer_demos', function (Blueprint $table) {
            $table->text('demo_link')->nullable()->change();
        });

        Schema::table('public_heroes', function (Blueprint $table) {
            $table->text('facebook_url')->nullable()->change();
            $table->text('instagram_url')->nullable()->change();
            $table->text('whatsapp_url')->nullable()->change();
            $table->text('linked_in_url')->nullable()->change();
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->text('link')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_demos', function (Blueprint $table) {
            $table->string('demo_link')->nullable()->change();
        });

        Schema::table('public_heroes', function (Blueprint $table) {
            $table->string('facebook_url')->nullable()->change();
            $table->string('instagram_url')->nullable()->change();
            $table->string('whatsapp_url')->nullable()->change();
            $table->string('linked_in_url')->nullable()->change();
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('link')->nullable()->change();
        });
    }
};
