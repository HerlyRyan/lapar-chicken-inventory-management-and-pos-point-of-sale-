<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixAvatarPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:avatar-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix avatar paths in the users table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNotNull('avatar')->get();
        
        foreach ($users as $user) {
            if (!str_starts_with($user->avatar, 'storage/')) {
                $oldPath = $user->avatar;
                $user->avatar = 'storage/avatars/' . basename($user->avatar);
                $user->save();
                $this->info("Updated user {$user->id}: {$oldPath} -> {$user->avatar}");
            } else {
                $this->line("Skipping user {$user->id}: Path already correct");
            }
        }
        
        $this->info('Avatar paths have been updated successfully!');
    }
}
