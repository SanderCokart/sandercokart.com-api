<?php

namespace App\Events;

use App\Models\File;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileModelDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public File $file;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(File $file)
    {

        $this->file = $file;
    }
}
