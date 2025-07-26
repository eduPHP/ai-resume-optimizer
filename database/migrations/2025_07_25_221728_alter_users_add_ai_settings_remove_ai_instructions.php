<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('ai_settings')->after('ai_instructions')->nullable();
        });
        DB::table('users')->whereNotNull('ai_instructions')->update([
            'ai_settings' => '{"instructions": "", "compatibilityScoreLevels": {"top": 95, "high": 90, "medium": 80, "low": 70}}',
        ]);
        DB::table('users')->whereNotNull('ai_instructions')->update([
            'ai_settings->instructions' => DB::raw('ai_instructions'),
        ]);
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ai_instructions');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('ai_instructions')->after('ai_settings')->nullable();
        });

        DB::statement('
            UPDATE `users`
            SET `ai_instructions` = JSON_UNQUOTE(JSON_EXTRACT(ai_settings, "$.instructions"))
            WHERE JSON_VALID(ai_settings)
        ');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ai_settings');
        });
    }
};
