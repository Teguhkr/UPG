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
        Schema::create('eselonerings', function (Blueprint $table) {
            $table->id();
            $table->string('token_id');
            $table->string('kode_eselon', 20);
            $table->string('eselonering', 225);
            $table->string('esel_name', 225);
            $table->integer('inspektorat');
            $table->string('nama_eselon', 225);
            $table->integer('no_urut');
            $table->text('metabase');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eselonerings');
    }
};
