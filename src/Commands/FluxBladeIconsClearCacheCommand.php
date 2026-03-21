<?php

declare(strict_types=1);

namespace Elemind\FluxBladeIcons\Commands;

use Elemind\FluxBladeIcons\IconListCache;
use Elemind\FluxBladeIcons\IconSetRegistry;
use Illuminate\Console\Command;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

class FluxBladeIconsClearCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flux:blade-icons:clear-cache {--set= : The icon set slug to clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear cached icon lists used by flux:blade-icons.';

    public function __construct(
        private readonly IconSetRegistry $iconSets,
        private readonly IconListCache $iconListCache,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $setKey = trim((string) $this->option('set'));

        if ($setKey !== '') {
            if (! $this->iconSets->has($setKey)) {
                error("Unknown icon set: {$setKey}");
                info('Available sets: '.implode(', ', $this->iconSets->keys()));

                return self::FAILURE;
            }

            $this->iconListCache->forget($setKey);

            info("Cleared cached icon list for {$setKey}.");

            return self::SUCCESS;
        }

        $setKeys = $this->iconSets->keys();
        $this->iconListCache->forgetMany($setKeys);

        info(sprintf('Cleared cached icon lists for %d icon sets.', count($setKeys)));

        return self::SUCCESS;
    }
}
