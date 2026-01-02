<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listvps', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->integer('price_month');
            $table->integer('price_year');
            $table->text('specs')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('time', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listvps');
    }
};

