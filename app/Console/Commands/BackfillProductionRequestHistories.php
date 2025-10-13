<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductionRequest;
use App\Models\ProductionRequestHistory;
use Illuminate\Support\Facades\DB;

class BackfillProductionRequestHistories extends Command
{
    protected $signature = 'production:backfill-approval-history {--dry-run} {--chunk=200}';

    protected $description = 'Backfill approval/rejection histories for existing production requests';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $chunk = (int) $this->option('chunk');

        $this->info('Starting backfill for production request histories' . ($dryRun ? ' (dry-run)' : ''));

        $totalProcessed = 0;
        $totalInserted = 0;

        ProductionRequest::whereIn('status', ['approved', 'rejected'])
            ->orderBy('id')
            ->chunk($chunk, function ($requests) use (&$totalProcessed, &$totalInserted, $dryRun) {
                foreach ($requests as $req) {
                    $totalProcessed++;

                    // Determine action and fields
                    $action = $req->status;
                    $actedBy = $req->approved_by; // approver id for both approved/rejected
                    $actedAt = $req->approved_at; // timestamp for both approved/rejected
                    $notes = $action === 'approved' ? $req->approval_notes : $req->rejection_reason;

                    // Skip if missing essentials
                    if (!$actedBy || !$actedAt) {
                        $this->line("- Skipping #{$req->id} ({$req->request_number}): missing acted_by/acted_at");
                        continue;
                    }

                    // Idempotency: if a history with same action already exists, skip
                    $exists = $req->histories()
                        ->where('action', $action)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    if ($dryRun) {
                        $this->line("Would insert history for #{$req->id} ({$req->request_number}) action={$action}");
                    } else {
                        ProductionRequestHistory::create([
                            'production_request_id' => $req->id,
                            'action' => $action,
                            'notes' => $notes,
                            'acted_by' => $actedBy,
                            'acted_at' => $actedAt,
                        ]);
                    }

                    $totalInserted++;
                }
            });

        $this->info("Processed: {$totalProcessed}, Inserted: {$totalInserted}" . ($dryRun ? ' (dry-run)' : ''));
        return self::SUCCESS;
    }
}
