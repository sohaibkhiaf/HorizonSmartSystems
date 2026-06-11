<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_replies', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('ticket_id')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('ticket_replies', 'author_type')) {
                $table->string('author_type')->default('client')->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_replies', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_replies', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }

            if (Schema::hasColumn('ticket_replies', 'author_type')) {
                $table->dropColumn('author_type');
            }
        });
    }
};
