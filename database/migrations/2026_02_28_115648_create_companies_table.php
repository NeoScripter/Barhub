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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exhibition_id')->constrained()->cascadeOnDelete();
            $table->string('public_name');
            $table->text('description');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('site_url');
            $table->string('instagram');
            $table->string('telegram');
            $table->text('activities');
            $table->string('legal_name');
            $table->unsignedInteger('stand_code');
            $table->boolean('show_on_site');
            $table->unsignedInteger('stand_area');
            $table->unsignedInteger('power_kw');
            $table->boolean('storage_enabled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
