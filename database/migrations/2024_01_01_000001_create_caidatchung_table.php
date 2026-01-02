<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caidatchung', function (Blueprint $table) {
            $table->id();
            $table->string('tieude', 255)->nullable();
            $table->string('theme', 255)->nullable();
            $table->text('keywords')->nullable();
            $table->text('mota')->nullable();
            $table->string('imagebanner', 255)->nullable();
            $table->string('sodienthoai', 255)->nullable();
            $table->string('banner', 2555)->nullable();
            $table->string('logo', 2555)->nullable();
            $table->string('webgach', 2565)->nullable();
            $table->string('apikey', 2555)->nullable();
            $table->string('callback', 255)->nullable();
            $table->string('facebook_link', 500)->nullable();
            $table->string('zalo_phone', 50)->nullable();
            $table->string('telegram_bot_token', 255)->nullable()->comment('Telegram Bot Token');
            $table->string('telegram_admin_chat_id', 255)->nullable()->comment('Telegram Admin Chat ID');
            $table->index('theme');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caidatchung');
    }
};

