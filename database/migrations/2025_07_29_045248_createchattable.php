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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary key 
            $table->string('name', 100);
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('status', ['pending', 'verified', 'active', 'inactive'])->default('pending');
            $table->enum('status_mode', ['online', 'offline', 'away', 'do_not_disturb', 'be_right_back'])->default('offline');
            $table->string('image')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('type', ['super_admin', 'admin', 'moderator', 'general_user'])->default('general_user');
            $table->rememberToken();
            $table->string('otp', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamps();
        });

        // GROUPS TABLE
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // MESSAGES TABLE
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->string('receiver_type')->default('user'); // 'user' or 'group'
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('cascade');

            $table->text('message')->nullable();
            $table->string('file_path')->nullable();
            $table->string('type')->default('text'); // 'text' or 'file'
            $table->string('file_extension')->nullable();

            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('read_at')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->nullOnDelete();
        });

        // GROUP MEMBERS TABLE
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['group_id', 'user_id']);
        });

        // GROUP MESSAGE READS
        Schema::create('group_message_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_message_reads');
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('users');
    }
};
