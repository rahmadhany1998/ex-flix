<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use App\Jobs\CheckMembershipStatus;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

#[Signature('memberships:check')]
#[Description('Check and deactivate expired memberships')]
class CheckMemberships extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Bus::batch([
            new CheckMembershipStatus(),
        ])->then(function (Batch $batch) {
            Log::info('Membership checks completed');
        })->catch(function (Batch $batch, $e) {
            Log::error('Membership check failed: ' . $e->getMessage());
        })->finally(function (Batch $batch) {
            Log::info('Membership check finished');
        })->dispatch();
    }
}
