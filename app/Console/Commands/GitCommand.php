<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'git:push {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add, commit, and push changes to Git';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $message = $this->argument('message');

        // Debugging: Display the message argument
        $this->info("Commit message: $message");

        // Execute git add
        $addOutput = shell_exec('git add .');
        $this->info("Git Add Output: " . trim($addOutput));

        // Execute git commit
        $commitOutput = shell_exec("git commit -m \"$message\"");
        $this->info("Git Commit Output: " . trim($commitOutput));

        // Execute git push
        $pushOutput = shell_exec('git push');
        $this->info("Git Push Output: " . trim($pushOutput));
    }
}
