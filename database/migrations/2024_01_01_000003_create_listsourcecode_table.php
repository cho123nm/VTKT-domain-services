<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listsourcecode', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->integer('price');
            $table->string('file_path', 500)->nullable();
            $table->string('download_link', 500)->nullable();
            $table->string('image', 500)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('time', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listsourcecode');
    }
};

