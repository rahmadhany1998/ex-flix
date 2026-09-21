<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use App\Jobs\CheckMembershipStatus;
use Illuminate\Console\Command;

#[Signature('memberships:check')]
#[Description('Check and deactivate expired memberships')]
class CheckMemberships extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        CheckMembershipStatus::dispatch();
        $this->info('Membership check job has been dispatched');
    }
}
