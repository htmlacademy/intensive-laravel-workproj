<?php

namespace Tests\Unit;

use App\Jobs\AddShow;
use Tests\TestCase;

class AddShowTest extends TestCase
{
    public function testProcessingJob()
    {
        AddShow::dispatchSync('tt0944947');
    }
}
