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
        Schema::table('store_subscriptions', function (Blueprint $table) {
            $table->string('ar_slug')
                ->nullable()
                ->unique()
                ->after('name');

            $table->string('fr_slug')
                ->nullable()
                ->unique()
                ->after('ar_slug');

            $table->string('en_slug')
                ->nullable()
                ->unique()
                ->after('fr_slug');
        });

        Schema::table('public_services', function (Blueprint $table) {
            $table->string('ar_slug')
                ->nullable()
                ->unique()
                ->after('en_name');

            $table->string('fr_slug')
                ->nullable()
                ->unique()
                ->after('ar_slug');

            $table->string('en_slug')
                ->nullable()
                ->unique()
                ->after('fr_slug');
        });

        Schema::table('public_software', function (Blueprint $table) {
            $table->string('ar_slug')
                ->nullable()
                ->unique()
                ->after('en_name');

            $table->string('fr_slug')
                ->nullable()
                ->unique()
                ->after('ar_slug');

            $table->string('en_slug')
                ->nullable()
                ->unique()
                ->after('fr_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_subscriptions', function (Blueprint $table) {
            $table->dropUnique(['ar_slug']);
            $table->dropUnique(['fr_slug']);
            $table->dropUnique(['en_slug']);

            $table->dropColumn([
                'ar_slug',
                'fr_slug',
                'en_slug',
            ]);
        });

        Schema::table('public_services', function (Blueprint $table) {
            $table->dropUnique(['ar_slug']);
            $table->dropUnique(['fr_slug']);
            $table->dropUnique(['en_slug']);

            $table->dropColumn([
                'ar_slug',
                'fr_slug',
                'en_slug',
            ]);
        });

        Schema::table('public_software', function (Blueprint $table) {
            $table->dropUnique(['ar_slug']);
            $table->dropUnique(['fr_slug']);
            $table->dropUnique(['en_slug']);

            $table->dropColumn([
                'ar_slug',
                'fr_slug',
                'en_slug',
            ]);
        });
    }
};

