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
        // 1. Make email nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 1024)->nullable()->change();
        });

        // 2. Add username as nullable first because old users already exist
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->after('name');
        });

        // 3. Fill existing users with fake usernames: username1, username2, ...
        $counter = 1;

        DB::table('users')
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($users) use (&$counter) {
                foreach ($users as $user) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'username' => 'username' . $counter,
                        ]);

                    $counter++;
                }
            });

        // 4. Make username required and unique
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable(false)->change();
            $table->unique('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Fill null emails before making email required again
        DB::table('users')
            ->whereNull('email')
            ->update([
                'email' => DB::raw("CONCAT('rollback_user_', id, '@example.test')"),
            ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');

            $table->string('email', 1024)->nullable(false)->change();
        });
    }
};
