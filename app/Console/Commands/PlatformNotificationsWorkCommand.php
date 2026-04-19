<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PlatformNotificationsWorkCommand extends Command
{
    protected $signature = 'platform-notifications:work';

    protected $description = 'Process platform notification jobs until the queue becomes empty';

    public function handle(): int
    {
        return Artisan::call('queue:work', [
            '--queue' => 'platform-notifications',
            '--stop-when-empty' => true,
            '--tries' => 1,
            '--sleep' => 1,
        ]);
    }
}
