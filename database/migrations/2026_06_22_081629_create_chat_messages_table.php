<?php

use App\Models\User;
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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();

            /*
             * Customer conversation owner.
             * Nullable because internal admin/operator messages may not belong to a customer.
             */
            $table->foreignIdFor(User::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            /*
             * Real sender and receiver.
             * Nullable because old/admin/system/group messages may not always have both sides.
             */
            $table->foreignId('sender_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('receiver_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * For group/broadcast messages.
             */
            $table->foreignId('customer_group_id')
                ->nullable()
                ->constrained('customer_groups')
                ->nullOnDelete();

            /*
             * Snapshot names.
             * Useful if a user is deleted or renamed later.
             */
            $table->string('sender_name')->nullable();
            $table->string('receiver_name')->nullable();

            /*
             * Message type.
             * "admin" can represent admin/operator messages.
             */
            $table->enum('sender_type', ['customer', 'admin'])->default('customer');

            $table->text('message');

            $table->boolean('is_seen')->default(false);

            $table->string('link')->nullable();
            $table->string('image')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['sender_id', 'created_at']);
            $table->index(['receiver_id', 'created_at']);
            $table->index(['customer_group_id', 'created_at']);
            $table->index(['sender_type', 'is_seen']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
