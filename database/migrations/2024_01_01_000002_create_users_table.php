<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('taikhoan', 255)->nullable();
            $table->string('matkhau', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->integer('tien')->default(0);
            $table->integer('chucvu')->default(0);
            $table->string('time', 255)->nullable();
            $table->index('taikhoan');
            $table->index('email');
            $table->index('chucvu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

