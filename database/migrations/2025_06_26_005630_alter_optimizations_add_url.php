<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('optimizations', function (Blueprint $table) {
            $table->string('role_url')->nullable()->after('role_description');
        });
    }

    public function down(): void
    {
        Schema::table('optimizations', function (Blueprint $table) {
            $table->dropColumn('role_url');
        });
    }
};
