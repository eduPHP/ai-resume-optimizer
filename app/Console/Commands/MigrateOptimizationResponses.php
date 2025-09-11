<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Optimization;

class MigrateOptimizationResponses extends Command
{
    protected $signature = 'resume:migrate-db-responses {--dry-run}';
    protected $description = 'Convert old AI response JSON in Optimization.ai_response to the new unified findings schema';

    public function handle(): int
    {
        $this->info("Starting migration of Optimization.ai_response...");

        $records = Optimization::whereNotNull('ai_response')->get();

        foreach ($records as $record) {
            $old = $record->ai_response;
            if (empty($old) || (isset($old['findings']) && isset($old['usage']))) {
                continue;
            }

            $new = $this->convertToNewSchema($old);

            if ($this->option('dry-run')) {
                $this->info("ID {$record->id} would be updated. usage: {$new['usage']}");
            } else {
                $record->ai_response = $new;
                $record->usage_tokens = $new['usage'];
                $record->save();
                $this->info("ID {$record->id} updated successfully.");
            }
        }

        $this->info("Migration completed.");

        return self::SUCCESS;
    }

    protected function convertToNewSchema(array $old): array
    {
        $findings = $old['findings'] ?? [];

        foreach (['strong_alignments' => 'strong_alignment',
                  'moderate_gaps' => 'moderate_gap',
                  'missing_requirements' => 'missing_requirement'] as $key => $group) {
            if (!empty($old[$key]) && is_array($old[$key])) {
                foreach ($old[$key] as $item) {
                    $findings[] = [
                        'group' => $group,
                        'title' => $item['title'] ?? '',
                        'description' => $item['description'] ?? '',
                    ];
                }
            }
        }

        $normalized = [
            'resume' => $old['resume'] ?? '',
            'compatibility_score' => $old['compatibility_score'] ?? 0,
            'professional_summary' => $old['professional_summary'] ?? '',
            'cover_letter' => $old['cover_letter'] ?? [],
            'findings' => $findings,
            'reasoning' => $old['reasoning'] ?? '',
            'top_choice' => $old['top_choice'] ?? '',
        ];

        // If old payload already had usage, keep it; otherwise estimate
        $normalized['usage'] = $old['usage'] ?? $this->estimateTokens($normalized);

        return $normalized;
    }

    /**
     * Approximate token usage:
     * tokens ≈ ceil(total_characters / 4)
     */
    protected function estimateTokens(array $data): int
    {
        // let's apply KISS here
        $chars = strlen(json_encode($data));

        // Conservative floor to at least a minimal overhead
        $approx = (int) ceil(max($chars, 1) / 4);

        // Add overhead to account for prompt/system tokens we don't have here
        $overhead = (int) ceil($approx * 0.65);

        return $approx + $overhead;
    }
}
