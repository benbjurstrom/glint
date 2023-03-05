<?php

namespace BenBjurstrom\Glinty\Commands;

use Illuminate\Console\Command;

class GlintyCommand extends Command
{
    public $signature = 'glinty';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
