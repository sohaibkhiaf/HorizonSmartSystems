<?php

use App\Models\PublicSoftware;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::table('customer_demos', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_demos', 'public_software_id')) {
                $table->foreignIdFor(PublicSoftware::class)
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->cascadeOnDelete();
            }
        });

        Schema::table('customer_demos', function (Blueprint $table) {
            if (Schema::hasColumn('customer_demos', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_demos', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_demos', 'user_id')) {
                $table->foreignIdFor(User::class)
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->cascadeOnDelete();
            }
        });

        Schema::table('customer_demos', function (Blueprint $table) {
            if (Schema::hasColumn('customer_demos', 'public_software_id')) {
                $table->dropForeign(['public_software_id']);
                $table->dropColumn('public_software_id');
            }
        });
    }
};
