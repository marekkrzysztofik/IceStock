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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_storage_id')->nullable()->references('id')->on('storages')->onDelete('cascade');
            $table->foreignId('destination_storage_id')->references('id')->on('storages')->onDelete('cascade');
            $table->foreignId('ice_cream_id')->references('id')->on('ice_creams')->onDelete('cascade');
            $table->integer('quantity');
            $table->enum('status', ['oczekujacy', 'zatwierdzony','production'])->default('oczekujacy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
