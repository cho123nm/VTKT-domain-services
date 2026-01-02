<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uid')->nullable();
            $table->string('domain', 255)->nullable();
            $table->string('ns1', 255)->nullable();
            $table->string('ns2', 255)->nullable();
            $table->integer('hsd')->nullable();
            $table->integer('status')->nullable();
            $table->string('mgd', 255)->nullable();
            $table->string('time', 255)->nullable();
            $table->string('timedns', 255)->nullable();
            $table->integer('ahihi')->nullable();
            $table->index('uid');
            $table->index('domain');
            $table->index('status');
            $table->index('mgd');
            $table->foreign('uid')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history');
    }
};

