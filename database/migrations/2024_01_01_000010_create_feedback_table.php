<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uid')->nullable()->comment('ID người dùng gửi phản hồi');
            $table->string('username', 255)->nullable()->comment('Tên người dùng');
            $table->string('email', 255)->nullable()->comment('Email người dùng');
            $table->text('message')->nullable()->comment('Nội dung phản hồi/lỗi');
            $table->text('admin_reply')->nullable()->comment('Phản hồi từ admin');
            $table->integer('status')->default(0)->comment('0: Chờ xử lý, 1: Đã trả lời, 2: Đã đọc');
            $table->string('telegram_chat_id', 255)->nullable()->comment('Chat ID Telegram của user');
            $table->string('time', 255)->nullable()->comment('Thời gian gửi');
            $table->string('reply_time', 255)->nullable()->comment('Thời gian admin trả lời');
            $table->index('uid');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};

