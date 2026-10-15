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
    Schema::create('service_entry_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('service_entry_id')->constrained('service_entries')->cascadeOnDelete();
        $table->foreignId('service_id')->constrained('services');
        $table->decimal('price', 8, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_entry_items');
    }
};
