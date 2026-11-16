<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category_type', 32)->default('faq')->index(); // faq | contoh_kasus
            $table->string('question');
            $table->string('slug')->unique();
            $table->longText('answer');
            $table->boolean('is_published')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('hits')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};

