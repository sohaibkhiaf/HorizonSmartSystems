<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
/**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Temporarily allow both old and new values
        DB::statement("
            ALTER TABLE `store_subscriptions`
            MODIFY `type` ENUM('key', 'account', 'invitation', 'activation_link')
            NULL DEFAULT NULL
        ");

        // 2. Update existing records
        DB::table('store_subscriptions')
            ->where('type', 'invitation')
            ->update([
                'type' => 'activation_link',
            ]);

        // 3. Remove the old value from the enum
        DB::statement("
            ALTER TABLE `store_subscriptions`
            MODIFY `type` ENUM('key', 'account', 'activation_link')
            NULL DEFAULT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Temporarily allow both values again
        DB::statement("
            ALTER TABLE `store_subscriptions`
            MODIFY `type` ENUM('key', 'account', 'invitation', 'activation_link')
            NULL DEFAULT NULL
        ");

        // 2. Convert activation_link back to invitation
        DB::table('store_subscriptions')
            ->where('type', 'activation_link')
            ->update([
                'type' => 'invitation',
            ]);

        // 3. Restore the old enum
        DB::statement("
            ALTER TABLE `store_subscriptions`
            MODIFY `type` ENUM('key', 'account', 'invitation')
            NULL DEFAULT NULL
        ");
    }
};
