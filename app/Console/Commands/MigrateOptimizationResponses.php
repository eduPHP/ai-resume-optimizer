<?php

namespace App\Console\Commands;

use App\Enums\OptimizationStatuses;
use Illuminate\Console\Command;
use App\Models\Optimization;

class MigrateOptimizationResponses extends Command
{
    protected $signature = 'resume:migrate-db-responses {--dry-run}';
    protected $description = 'Convert old AI response JSON in Optimization.ai_response to the new unified findings schema';

    public function handle(): int
    {
        $this->info("Starting migration of Optimization.ai_response...");

        $records = Optimization::where('status', OptimizationStatuses::Complete)->get();

        foreach ($records as $record) {
            $old = $record->ai_response;
            if (empty($old)) {
                continue;
            }

            $new = $this->convertToNewSchema($old);

            if ($this->option('dry-run')) {
                $this->info("ID {$record->id} would be updated.");
            } else {
                $record->ai_response = $new;
                $record->save();
                $this->info("ID {$record->id} updated successfully.");
            }
        }

        $this->info("Migration completed.");

        return self::SUCCESS;
    }

    protected function convertToNewSchema(array $old): array
    {
        $findings = [];

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

        return [
            'resume' => $old['resume'] ?? '',
            'compatibility_score' => $old['compatibility_score'] ?? 0,
            'professional_summary' => $old['professional_summary'] ?? '',
            'cover_letter' => $old['cover_letter'] ?? [],
            'findings' => $findings,
            'reasoning' => $old['reasoning'] ?? '',
            'top_choice' => $old['top_choice'] ?? '',
        ];
    }
}
