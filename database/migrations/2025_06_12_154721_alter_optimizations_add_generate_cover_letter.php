<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('optimizations', function (Blueprint $table) {
            $table->boolean('generate_cover_letter')->default(false)->after('change_professional_summary');
        });
    }

    public function down(): void
    {
        Schema::table('optimizations', function (Blueprint $table) {
            $table->dropColumn('generate_cover_letter');
        });
    }
};
