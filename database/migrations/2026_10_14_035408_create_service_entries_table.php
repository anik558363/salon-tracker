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
    Schema::create('service_entries', function (Blueprint $table) {
        $table->id();
        $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
        $table->date('entry_date');
        $table->decimal('total_amount', 10, 2);
        $table->text('note')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_entries');
    }
};
