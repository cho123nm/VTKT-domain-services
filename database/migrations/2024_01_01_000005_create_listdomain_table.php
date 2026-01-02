<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listdomain', function (Blueprint $table) {
            $table->id();
            $table->string('image', 2655)->nullable();
            $table->string('price', 2555)->nullable();
            $table->string('duoi', 255)->nullable();
            $table->index('duoi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listdomain');
    }
};

