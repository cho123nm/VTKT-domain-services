<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vpshistory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uid');
            $table->unsignedBigInteger('vps_id');
            $table->string('period', 20);
            $table->string('mgd', 100);
            $table->string('time', 50)->nullable();
            $table->integer('status')->default(0);
            $table->index('uid');
            $table->index('vps_id');
            $table->index('mgd');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vpshistory');
    }
};

