<?php

namespace Threls\LaravelMoney\Commands;

use Illuminate\Console\Command;

class LaravelMoneyCommand extends Command
{
    public $signature = 'laravelmoney';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
