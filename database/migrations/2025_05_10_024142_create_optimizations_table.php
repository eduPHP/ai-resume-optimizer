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
        Schema::create('optimizations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('role_name');
            $table->string('role_company');
            $table->string('role_description');
            $table->foreignIdFor(\App\Models\User::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Resume::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('role_location')->nullable();
            $table->tinyInteger('current_step')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optimizations');
    }
};
