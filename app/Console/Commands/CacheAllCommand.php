<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CacheAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache all laravel assets';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('cache:clear');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');
    }
}
