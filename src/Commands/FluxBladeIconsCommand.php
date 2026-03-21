<?php

namespace Elemind\FluxBladeIcons\Commands;

use Illuminate\Console\Command;

class FluxBladeIconsCommand extends Command
{
    public $signature = 'flux-blade-icons';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
