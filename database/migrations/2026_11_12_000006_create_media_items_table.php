<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();
            $table->string('type', 32)->default('infografis')->index(); // infografis | video | gallery
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('embed_url')->nullable();
            $table->string('album')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_items');
    }
};

