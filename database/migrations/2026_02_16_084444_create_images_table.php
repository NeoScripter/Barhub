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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('webp3x');
            $table->string('webp2x');
            $table->string('webp');
            $table->string('avif3x');
            $table->string('avif2x');
            $table->string('avif');
            $table->string('tiny');
            $table->text('alt');
            $table->morphs('imageable');
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
