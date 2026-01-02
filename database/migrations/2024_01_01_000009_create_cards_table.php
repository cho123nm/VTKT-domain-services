<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uid')->nullable();
            $table->string('pin', 255)->nullable();
            $table->string('serial', 255)->nullable();
            $table->string('type', 255)->nullable();
            $table->string('amount', 255)->nullable();
            $table->string('requestid', 255)->nullable();
            $table->integer('status')->nullable();
            $table->string('time', 255)->nullable();
            $table->string('time2', 255)->nullable();
            $table->string('time3', 255)->nullable();
            $table->index('uid');
            $table->index('status');
            $table->index('requestid');
            $table->foreign('uid')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};

