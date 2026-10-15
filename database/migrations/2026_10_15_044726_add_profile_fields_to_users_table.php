<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('title')->nullable()->after('email'); // Hal. Senior Barber, Executive Stylist
            $table->text('bio')->nullable()->after('title');      // Maikling paglalarawan
            $table->string('photo')->nullable()->after('bio');    // Profile image
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['title', 'bio', 'photo']);
        });
    }
};
