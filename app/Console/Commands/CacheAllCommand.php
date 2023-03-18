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
    protected $signature = 'cache:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recache all the things!';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('optimize:clear');
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');

        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');
        $this->call('event:cache');
    }
}
