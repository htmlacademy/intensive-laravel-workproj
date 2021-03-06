<?php

namespace App\Jobs;

use App\Support\Import\ImportRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddShow implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private string $imdbId)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ImportRepository $repository)
    {
        $data = $repository->getShow($this->imdbId);

        if ($data) {
            SaveShow::dispatch($data);
        }
    }
}
