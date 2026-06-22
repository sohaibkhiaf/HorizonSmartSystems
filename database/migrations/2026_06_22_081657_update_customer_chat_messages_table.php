<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_chat_messages', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('customer_chat_messages', function (Blueprint $table) {
            // user_id becomes the customer conversation owner.
            // It is nullable because admin/operator internal messages do not belong to a customer.
            $table->unsignedBigInteger('user_id')->nullable()->change();

            $table->foreignId('sender_id')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('receiver_id')
                ->nullable()
                ->after('sender_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('customer_group_id')
                ->nullable()
                ->after('receiver_id')
                ->constrained('customer_groups')
                ->nullOnDelete();

            $table->string('sender_name')->nullable()->after('customer_group_id');
            $table->string('receiver_name')->nullable()->after('sender_name');

            $table->index(['user_id', 'created_at']);
            $table->index(['sender_id', 'created_at']);
            $table->index(['receiver_id', 'created_at']);
            $table->index(['sender_type', 'is_seen']);
        });

        Schema::table('customer_chat_messages', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        // Backfill old messages.
        DB::table('customer_chat_messages')
            ->orderBy('id')
            ->chunkById(200, function ($messages) {
                $userIds = $messages->pluck('user_id')->filter()->unique();

                $users = DB::table('users')
                    ->whereIn('id', $userIds)
                    ->pluck('name', 'id');

                foreach ($messages as $message) {
                    if ($message->sender_type === 'customer') {
                        DB::table('customer_chat_messages')
                            ->where('id', $message->id)
                            ->update([
                                'sender_id' => $message->user_id,
                                'receiver_id' => null,
                                'sender_name' => $users[$message->user_id] ?? 'Client',
                                'receiver_name' => 'Administration',
                            ]);
                    } else {
                        DB::table('customer_chat_messages')
                            ->where('id', $message->id)
                            ->update([
                                'sender_id' => null,
                                'receiver_id' => $message->user_id,
                                'sender_name' => 'Admin',
                                'receiver_name' => $users[$message->user_id] ?? 'Client',
                            ]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('customer_chat_messages', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);
            $table->dropForeign(['customer_group_id']);

            $table->dropColumn([
                'sender_id',
                'receiver_id',
                'customer_group_id',
                'sender_name',
                'receiver_name',
            ]);
        });
    }
};
