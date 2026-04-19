<?php

namespace App\Services\Queue;

use Illuminate\Support\Facades\Log;

class QueueWorkerLauncher
{
    public function launchPlatformNotificationsWorker(): void
    {
        try {
            $phpBinary = escapeshellarg(PHP_BINARY);
            $artisan = escapeshellarg(base_path('artisan'));
            $basePath = escapeshellarg(base_path());

            if (PHP_OS_FAMILY === 'Windows') {
                $command = "cmd /c \"cd /d {$basePath} && start \"\" /B {$phpBinary} {$artisan} platform-notifications:work\"";
                @pclose(@popen($command, 'r'));

                return;
            }

            $command = "{$phpBinary} {$artisan} platform-notifications:work > /dev/null 2>&1 &";
            @exec($command);
        } catch (\Throwable $exception) {
            Log::warning('Unable to launch platform notifications queue worker.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
